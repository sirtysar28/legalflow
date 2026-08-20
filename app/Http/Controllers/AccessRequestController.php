<?php

namespace App\Http\Controllers;

use App\Enums\AccessStatus;
use App\Models\Application;
use App\Models\DocumentAccessRequest;
use App\Models\User;
use App\Notifications\ApplicationNotification;
use Illuminate\Http\Request;

class AccessRequestController extends Controller
{
    /**
     * User lain meminta akses dokumen pengajuan yang sudah APPROVED.
     */
    public function store(Request $request, Application $application)
    {
        $user = auth()->user();

        abort_unless($application->status === \App\Enums\ApplicationStatus::APPROVED, 403,
            'Akses hanya dapat diminta untuk dokumen yang sudah disetujui.');
        abort_if($application->isOwnedBy($user), 403, 'Anda pemilik pengajuan ini.');

        $request->validate([
            'reason' => ['required', 'string', 'max:1000'],
        ], [
            'reason.required' => 'Alasan permintaan akses wajib diisi.',
        ]);

        $existing = DocumentAccessRequest::where('application_id', $application->id)
            ->where('requested_by', $user->id)
            ->whereIn('status', [AccessStatus::REQUESTED->value, AccessStatus::APPROVED->value])
            ->first();

        if ($existing) {
            return back()->with('info', 'Anda sudah memiliki permintaan akses '
                . strtolower($existing->status->label()) . ' untuk dokumen ini.');
        }

        $accessRequest = DocumentAccessRequest::create([
            'application_id' => $application->id,
            'requested_by'   => $user->id,
            'reason'         => $request->input('reason'),
            'status'         => AccessStatus::REQUESTED->value,
        ]);

        $url = route('access-requests.incoming');

        User::whereHas('role', fn ($q) => $q->whereIn('name', ['legal', 'admin']))
            ->get()
            ->each(fn (User $reviewer) => $reviewer->notify(new ApplicationNotification(
                'Permintaan Akses Dokumen',
                "{$user->name} meminta akses dokumen {$application->application_number}.",
                $url
            )));

        return back()->with('success', 'Permintaan akses dikirim. Menunggu persetujuan Admin/Legal.');
    }

    /**
     * Daftar permintaan akses milik saya.
     */
    public function mine()
    {
        DocumentAccessRequest::syncExpiry();

        $requests = DocumentAccessRequest::with(['application.user', 'application.department', 'reviewer'])
            ->where('requested_by', auth()->id())
            ->latest()->paginate(10);

        return view('access.mine', compact('requests'));
    }

    /**
     * Daftar permintaan akses masuk untuk Legal/Admin.
     */
    public function incoming()
    {
        DocumentAccessRequest::syncExpiry();

        $requests = DocumentAccessRequest::with(['application.user', 'application.department', 'requester', 'reviewer'])
            ->latest()->paginate(10);

        return view('access.incoming', compact('requests'));
    }

    /**
     * Setujui permintaan akses + tentukan jenis & masa berlaku.
     */
    public function approve(Request $request, DocumentAccessRequest $accessRequest)
    {
        abort_unless(auth()->user()->canReview(), 403);

        $data = $request->validate([
            'access_type' => ['required', 'in:VIEW,DOWNLOAD'],
            'expired_at'  => ['required', 'date', 'after_or_equal:today'],
            'notes'       => ['nullable', 'string', 'max:1000'],
        ], [
            'expired_at.required'        => 'Masa berlaku akses wajib ditentukan.',
            'expired_at.after_or_equal'  => 'Masa berlaku minimal hari ini.',
        ]);

        abort_unless($accessRequest->status === AccessStatus::REQUESTED, 403,
            'Permintaan ini sudah diproses.');

        $accessRequest->update([
            'status'       => AccessStatus::APPROVED->value,
            'access_type'  => $data['access_type'],
            'expired_at'   => $data['expired_at'],
            'reviewed_by'  => auth()->id(),
            'review_notes' => $data['notes'] ?? null,
            'approved_at'  => now(),
        ]);

        $accessRequest->requester?->notify(new ApplicationNotification(
            'Akses Dokumen Disetujui',
            'Akses dokumen ' . $accessRequest->application->application_number
                . " disetujui ({$data['access_type']}) s/d {$data['expired_at']}.",
            route('applications.show', $accessRequest->application_id)
        ));

        return back()->with('success', 'Permintaan akses disetujui.');
    }

    /**
     * Tolak permintaan akses.
     */
    public function reject(Request $request, DocumentAccessRequest $accessRequest)
    {
        abort_unless(auth()->user()->canReview(), 403);
        abort_unless($accessRequest->status === AccessStatus::REQUESTED, 403,
            'Permintaan ini sudah diproses.');

        $data = $request->validate([
            'review_notes' => ['required', 'string', 'max:1000'],
        ], [
            'review_notes.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $accessRequest->update([
            'status'       => AccessStatus::REJECTED->value,
            'reviewed_by'  => auth()->id(),
            'review_notes' => $data['review_notes'],
        ]);

        $accessRequest->requester?->notify(new ApplicationNotification(
            'Akses Dokumen Ditolak',
            'Permintaan akses dokumen ' . $accessRequest->application->application_number
                . ' ditolak. Alasan: ' . $data['review_notes'],
            route('access-requests.mine')
        ));

        return back()->with('success', 'Permintaan akses ditolak.');
    }
}

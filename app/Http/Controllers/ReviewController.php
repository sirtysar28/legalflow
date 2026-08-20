<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Services\ApplicationWorkflowService;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct(private ApplicationWorkflowService $workflow)
    {
    }

    /**
     * Antrean review Legal/Admin.
     */
    public function queue(Request $request)
    {
        $status = ApplicationStatus::tryFrom((string) $request->query('status'));

        $query = Application::with(['user', 'department', 'permitType', 'supplier'])
            ->latest('submitted_at');

        if ($status) {
            $query->status($status);
        } else {
            $query->whereIn('status', [
                ApplicationStatus::SUBMITTED->value,
                ApplicationStatus::RESUBMITTED->value,
                ApplicationStatus::UNDER_REVIEW->value,
            ]);
        }

        return view('review.queue', [
            'applications' => $query->paginate(10)->withQueryString(),
            'status'       => $status,
            'statuses'     => ApplicationStatus::options(),
        ]);
    }

    /**
     * Mulai review: SUBMITTED|RESUBMITTED -> UNDER_REVIEW.
     */
    public function start(Application $application)
    {
        $this->authorizeReviewer();

        $this->workflow->startReview($application, auth()->user());

        return back()->with('success', 'Pengajuan mulai direview.');
    }

    /**
     * Keputusan Legal/Admin: approve / revision / reject.
     */
    public function decide(Request $request, Application $application)
    {
        $this->authorizeReviewer();

        $data = $request->validate([
            'action'      => ['required', 'in:approve,revision,reject'],
            'notes'       => ['nullable', 'string'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:today'],
        ], [
            'action.required'             => 'Pilih keputusan review.',
            'valid_until.after_or_equal'  => 'Masa berlaku minimal hari ini.',
        ]);

        if (in_array($data['action'], ['revision', 'reject'], true) && blank($data['notes'])) {
            return back()->withErrors(['notes' => 'Catatan wajib diisi untuk minta revisi / penolakan.']);
        }

        match ($data['action']) {
            'approve'  => $this->workflow->approve($application, auth()->user(), $data['notes'] ?? null, $data['valid_until'] ?? null),
            'revision' => $this->workflow->requestRevision($application, auth()->user(), $data['notes']),
            'reject'   => $this->workflow->reject($application, auth()->user(), $data['notes']),
        };

        $message = match ($data['action']) {
            'approve'  => 'Pengajuan disetujui. Dokumen terbit & tersimpan otomatis di folder divisi.',
            'revision' => 'Permintaan revisi dikirim ke pemohon.',
            'reject'   => 'Pengajuan ditolak.',
        };

        return redirect()->route('applications.show', $application)->with('success', $message);
    }

    private function authorizeReviewer(): void
    {
        abort_unless(auth()->user()->canReview(), 403, 'Hanya Legal/Admin yang dapat melakukan review.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\Document;
use App\Models\DocumentRequirement;
use App\Services\AccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    private const ALLOWED_MIMES = 'pdf,doc,docx,xls,xlsx,jpg,jpeg,png';

    /**
     * Upload dokumen ke pengajuan (status DRAFT / REVISION_REQUESTED).
     */
    public function store(Request $request, Application $application)
    {
        $this->authorizeOwner($application);
        abort_unless($application->isEditable(), 403, 'Dokumen hanya dapat diunggah saat Draft / Perlu Revisi.');

        $request->validate([
            'document_type' => ['required', 'string', 'max:255'],
            'file'          => ['required', 'file', 'mimes:' . self::ALLOWED_MIMES, 'max:10240'],
        ], [
            'file.required' => 'Pilih file terlebih dahulu.',
            'file.mimes'    => 'Format file harus: ' . self::ALLOWED_MIMES,
            'file.max'      => 'Ukuran file maksimal 10MB.',
        ]);

        $file = $request->file('file');
        $path = $file->store("documents/{$application->id}", 'local');

        $version = $application->documents()
            ->where('document_type', $request->input('document_type'))
            ->max('version') + 1;

        $document = $application->documents()->create([
            'document_type' => $request->input('document_type'),
            'file_name'     => $file->getClientOriginalName(),
            'file_path'     => $path,
            'mime_type'     => $file->getClientMimeType(),
            'size'          => $file->getSize(),
            'version'       => $version,
            'status'        => Document::STATUS_UPLOADED,
            'uploaded_by'   => auth()->id(),
        ]);

        ApplicationHistory::create([
            'application_id' => $application->id,
            'user_id'        => auth()->id(),
            'action'         => 'Upload dokumen',
            'old_status'     => $application->status->value,
            'new_status'     => $application->status->value,
            'notes'          => "{$document->document_type} — {$document->file_name} (v{$document->version})",
        ]);

        return back()->with('success', "Dokumen \"{$document->document_type}\" berhasil diunggah.");
    }

    /**
     * Hapus dokumen (hanya saat masih dapat diedit).
     */
    public function destroy(Application $application, Document $document)
    {
        $this->authorizeOwner($application);
        abort_unless($application->isEditable(), 403, 'Dokumen tidak dapat dihapus pada status saat ini.');
        abort_unless($document->application_id === $application->id, 404);

        Storage::disk('local')->delete($document->file_path);
        $document->delete();

        ApplicationHistory::create([
            'application_id' => $application->id,
            'user_id'        => auth()->id(),
            'action'         => 'Hapus dokumen',
            'old_status'     => $application->status->value,
            'new_status'     => $application->status->value,
            'notes'          => "{$document->document_type} — {$document->file_name}",
        ]);

        return back()->with('success', 'Dokumen berhasil dihapus.');
    }

    /**
     * Unduh dokumen dengan otorisasi akses.
     */
    public function download(Document $document)
    {
        abort_unless(AccessService::authorizeDocumentDownload(auth()->user(), $document), 403,
            'Anda tidak memiliki akses untuk mengunduh dokumen ini.');

        abort_if(! Storage::disk('local')->exists($document->file_path), 404, 'File tidak ditemukan.');

        return Storage::disk('local')->download($document->file_path, $document->file_name);
    }

    /**
     * Daftar dokumen terbit (APPROVED) milik seluruh divisi + status akses user.
     */
    public function browse(Request $request)
    {
        \App\Models\Application::syncExpiry();

        $query = \App\Models\Application::with(['user', 'department', 'documents'])
            ->status(\App\Enums\ApplicationStatus::APPROVED)
            ->withCount('documents');

        if ($request->filled('q')) {
            $q = $request->string('q');
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")
                ->orWhere('application_number', 'like', "%{$q}%"));
        }

        $applications = $query->latest('approved_at')->paginate(10)->withQueryString();

        $myRequests = \App\Models\DocumentAccessRequest::with('application')
            ->where('requested_by', auth()->id())->get()
            ->keyBy('application_id');

        return view('documents.browse', compact('applications', 'myRequests'));
    }

    private function authorizeOwner(Application $application): void
    {
        abort_unless($application->isOwnedBy(auth()->user()), 403, 'Anda bukan pemilik pengajuan ini.');
    }
}

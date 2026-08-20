@extends('layouts.app')
@php($user = auth()->user())
@section('title', 'Dokumen Tersimpan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Document Management — Dokumen Terbit</h4>
        <p class="text-muted mb-0">Dokumen tersimpan otomatis ke folder divisi setelah disetujui</p>
    </div>
    <div class="d-flex gap-2">
        @if ($user->canReview())
            <a href="{{ route('documents.folders') }}" class="btn btn-outline-primary">
                <i class="bi bi-diagram-2 me-1"></i>Kelola Folder
            </a>
        @endif
        <form method="GET" class="d-flex gap-2">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari judul / nomor...">
            <button class="btn btn-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
</div>

<div class="row g-3">
    @forelse ($applications as $application)
        @php($myRequest = $myRequests->get($application->id))
        <div class="col-md-6 col-xl-4">
            <div class="card lf-card h-100">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="badge bg-light text-dark"><i class="bi bi-folder2 me-1"></i>{{ $application->department?->name }}</span>
                        <span class="badge bg-success badge-status"><i class="bi bi-check-circle me-1"></i>Terbit</span>
                    </div>
                    <h6 class="fw-bold mb-1">{{ Str::limit($application->title, 55) }}</h6>
                    <p class="text-muted small mb-2">
                        <code>{{ $application->application_number }}</code> · {{ $application->typeLabel() }}
                    </p>
                    <p class="small text-muted mb-2">
                        <i class="bi bi-person me-1"></i>{{ $application->user->name }}
                        · <i class="bi bi-file-earmark me-1"></i>{{ $application->documents_count }} dokumen
                        · <i class="bi bi-calendar-check me-1"></i>{{ $application->approved_at?->format('d M Y') }}
                    </p>

                    <div class="mt-auto">
                        @if ($application->isOwnedBy($user) || $user->canReview())
                            <span class="badge bg-primary badge-status"><i class="bi bi-unlock me-1"></i>Akses Penuh</span>
                        @elseif ($myRequest)
                            <span class="badge badge-status bg-{{ $myRequest->status->color() }}">
                                {{ $myRequest->status->label() }}
                                @if ($myRequest->status === \App\Enums\AccessStatus::APPROVED && $myRequest->expired_at)
                                    · s/d {{ $myRequest->expired_at->format('d M Y') }}
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
                <div class="card-footer bg-white d-flex gap-2">
                    <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-outline-primary flex-fill">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                    @if (! $application->isOwnedBy($user) && ! $user->canReview() && ! $myRequest)
                        <button class="btn btn-sm btn-warning text-dark flex-fill" data-bs-toggle="modal"
                                data-bs-target="#accessModal{{ $application->id }}">
                            <i class="bi bi-key me-1"></i>Minta Akses
                        </button>
                    @endif
                </div>
            </div>
        </div>

        {{-- Modal minta akses --}}
        <div class="modal fade" id="accessModal{{ $application->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form method="POST" action="{{ route('access-requests.store', $application) }}" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Minta Akses Dokumen</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p class="small text-muted">
                            <strong>{{ $application->application_number }}</strong> — {{ $application->title }}
                        </p>
                        <label class="form-label">Alasan Permintaan <span class="text-danger">*</span></label>
                        <textarea name="reason" rows="3" class="form-control" required
                                  placeholder="Contoh: Dokumen diperlukan untuk proses review kontrak dengan supplier."></textarea>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark"><i class="bi bi-send me-1"></i>Kirim Permintaan</button>
                    </div>
                </form>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card lf-card"><div class="card-body text-center text-muted py-5">Belum ada dokumen terbit.</div></div>
        </div>
    @endforelse
</div>

<div class="mt-3">
    {{ $applications->links() }}
</div>
@endsection

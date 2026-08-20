@extends('layouts.app')
@php($user = auth()->user())
@section('title', 'Detail Pengajuan ' . $application->application_number)

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <h4 class="fw-bold mb-0">{{ $application->title }}</h4>
            <span class="badge bg-light text-dark">{{ $application->typeLabel() }}</span>
            <span class="badge badge-status bg-{{ $application->statusColor() }}">{{ $application->statusLabel() }}</span>
        </div>
        <p class="text-muted mt-1 mb-0">
            <code>{{ $application->application_number }}</code>
            @if ($application->valid_until && in_array($application->status, [\App\Enums\ApplicationStatus::APPROVED, \App\Enums\ApplicationStatus::EXPIRED]))
                · Berlaku s/d {{ $application->valid_until->format('d M Y') }}
                @if ($application->status === \App\Enums\ApplicationStatus::APPROVED)
                    <span class="badge bg-warning text-dark badge-status">
                        <i class="bi bi-hourglass-split me-1"></i>{{ $application->valid_until->diffForHumans() }}
                    </span>
                @endif
            @endif
        </p>
    </div>
    <div class="d-flex gap-2">
        @if ($application->isOwnedBy($user) && $application->isEditable())
            <a href="{{ route('applications.edit', $application) }}" class="btn btn-primary">
                <i class="bi bi-pencil-square me-1"></i>{{ $application->status === \App\Enums\ApplicationStatus::REVISION_REQUESTED ? 'Perbaiki & Unggah Ulang' : 'Edit' }}
            </a>
        @endif
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
    </div>
</div>

{{-- Catatan revisi terbaru --}}
@if ($application->status === \App\Enums\ApplicationStatus::REVISION_REQUESTED)
    @php($latestRevision = $application->reviews->firstWhere('action', \App\Models\ApplicationReview::ACTION_REVISION))
    @if ($latestRevision)
        <div class="alert alert-warning d-flex align-items-start gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 mt-1"></i>
            <div>
                <strong>Revisi diminta oleh {{ $latestRevision->reviewer?->name }}:</strong>
                <div>{{ $latestRevision->notes }}</div>
            </div>
        </div>
    @endif
@endif

<div class="row g-3">
    <div class="col-lg-8">
        {{-- ================= DETAIL 3-TAB ================= --}}
        <div class="card lf-card mb-3">
            <div class="card-header bg-white pt-3 px-3">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-semibold" data-bs-toggle="tab" data-bs-target="#tabInfo" type="button" role="tab">
                            <i class="bi bi-info-circle me-1"></i>Informasi
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabDocs" type="button" role="tab">
                            <i class="bi bi-folder2 me-1"></i>Dokumen
                            <span class="badge bg-secondary badge-status">{{ $application->documents->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-semibold" data-bs-toggle="tab" data-bs-target="#tabHistory" type="button" role="tab">
                            <i class="bi bi-clock-history me-1"></i>Riwayat
                            <span class="badge bg-secondary badge-status">{{ $application->histories->count() }}</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    {{-- ============ TAB 1: INFORMASI ============ --}}
                    <div class="tab-pane fade show active" id="tabInfo" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-6 mb-2"><span class="text-muted small">Pemohon</span><div>{{ $application->user->name }}</div></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted small">Divisi</span><div>{{ $application->department?->name ?? '-' }}</div></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted small">Tanggal Pengajuan</span><div>{{ $application->submitted_at?->format('d M Y H:i') ?? '-' }}</div></div>
                            <div class="col-sm-6 mb-2"><span class="text-muted small">Disetujui</span><div>{{ $application->approved_at?->format('d M Y H:i') ?? '-' }}</div></div>
                            @if ($application->permit_type_id)
                                <div class="col-sm-6 mb-2">
                                    <span class="text-muted small">Jenis Izin</span>
                                    <div>{{ $application->permitType?->name }}
                                        @if ($application->permitType?->category)
                                            <span class="badge bg-light text-dark">{{ $application->permitType->categoryLabel() }}</span>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if ($application->application_type === \App\Enums\ApplicationType::PERMIT)
                                <div class="col-sm-6 mb-2">
                                    <span class="text-muted small">Lokasi / Objek Izin</span>
                                    <div>{{ $application->fieldValue('lokasi') ?: $application->department?->name ?? '-' }}</div>
                                </div>
                            @endif
                            @if ($application->application_type === \App\Enums\ApplicationType::AGREEMENT)
                                <div class="col-sm-6 mb-2"><span class="text-muted small">Nilai Kontrak</span><div>{{ $application->contractSummary() ?? '-' }}</div></div>
                                <div class="col-sm-6 mb-2">
                                    <span class="text-muted small">Jenis Pihak</span>
                                    <div>{{ $application->fieldValue('jenis_pihak') ?: '-' }}</div>
                                </div>
                            @endif
                            @if ($application->valid_until)
                                <div class="col-sm-6 mb-2">
                                    <span class="text-muted small">Masa Berlaku</span>
                                    <div>s/d {{ $application->valid_until->format('d M Y') }}</div>
                                </div>
                            @endif
                            <div class="col-12"><span class="text-muted small">Deskripsi / Catatan Pemohon</span><div>{{ $application->description ?? '-' }}</div></div>
                        </div>

                        {{-- Supplier Assessment otomatis (khusus Agreement) --}}
                        @if ($application->supplier_id)
                            @php($assessment = $application->supplier?->assessmentSummary())
                            <hr>
                            <h6 class="fw-semibold mb-3"><i class="bi bi-clipboard2-data me-2"></i>Supplier Assessment System (otomatis)</h6>
                            <div class="row g-2 mb-2">
                                <div class="col-sm-4">
                                    <div class="border rounded-3 p-2 text-center">
                                        <div class="text-muted small">Status</div>
                                        <span class="badge bg-{{ $assessment['passed'] ? 'success' : 'warning text-dark' }} badge-status">
                                            {{ $assessment['passed'] ? 'Lolos' : 'Belum Lolos' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="border rounded-3 p-2 text-center">
                                        <div class="text-muted small">Risk Level</div>
                                        <span class="badge bg-{{ $assessment['risk_color'] }} badge-status">{{ $assessment['risk'] }}</span>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="border rounded-3 p-2 text-center">
                                        <div class="text-muted small">Skor</div>
                                        <strong>{{ $assessment['score'] ?? '-' }}</strong>
                                        <span class="text-muted small">({{ $assessment['date'] ?? 'belum ada' }})</span>
                                    </div>
                                </div>
                            </div>
                            <div class="text-muted small">
                                <i class="bi bi-person me-1"></i>{{ $application->supplier?->name }}
                                · {{ $application->supplier?->is_registered ? 'Terdaftar' : 'Belum terdaftar' }}
                                · {{ $application->supplier?->data_complete ? 'Data lengkap' : 'Data belum lengkap' }}
                                · {{ $application->supplier?->documents_complete ? 'Dokumen lengkap' : 'Dokumen belum lengkap' }}
                            </div>
                        @endif

                        {{-- Progress kelengkapan dokumen --}}
                        @php($progress = $application->documentProgress())
                        <hr>
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold">Persentase Kelengkapan Dokumen</span>
                            <span>{{ $progress['uploaded'] }}/{{ $progress['total'] }} wajib ({{ $progress['percent'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar {{ $progress['percent'] >= 100 ? 'bg-success' : 'bg-primary' }}"
                                 style="width: {{ $progress['percent'] }}%"></div>
                        </div>

                        {{-- Detail field dinamis --}}
                        <hr>
                        <div class="row">
                            @include('applications.partials.fields', ['application' => $application])
                        </div>
                    </div>

                    {{-- ============ TAB 2: DOKUMEN ============ --}}
                    <div class="tab-pane fade" id="tabDocs" role="tabpanel">
                        {{-- Checklist persyaratan --}}
                        <h6 class="fw-semibold mb-3">Checklist Persyaratan</h6>
                        <ul class="list-unstyled">
                            @forelse ($requirements as $requirement)
                                @php($uploadedDoc = $application->documents->firstWhere('document_type', $requirement->document_name))
                                <li class="border rounded-3 p-2 mb-2 d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold small">
                                            @if ($uploadedDoc)
                                                <i class="bi bi-check-circle-fill req-done me-1"></i>
                                            @elseif ($requirement->is_required)
                                                <i class="bi bi-exclamation-circle-fill req-missing me-1"></i>
                                            @else
                                                <i class="bi bi-dash-circle text-muted me-1"></i>
                                            @endif
                                            {{ $requirement->document_name }}
                                        </div>
                                        @if ($uploadedDoc)
                                            <div class="small text-muted">
                                                {{ $uploadedDoc->file_name }} · {{ $uploadedDoc->sizeHuman() }} · v{{ $uploadedDoc->version }}
                                            </div>
                                        @elseif ($requirement->is_required)
                                            <div class="small text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Belum diunggah</div>
                                        @endif
                                    </div>
                                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                                        <span class="badge bg-{{ $requirement->is_required ? 'danger' : 'secondary' }} badge-status">
                                            {{ $requirement->is_required ? 'WAJIB' : 'OPSIONAL' }}
                                        </span>
                                        @if ($uploadedDoc && \App\Services\AccessService::canAccessDocuments($user, $application))
                                            <a href="{{ route('documents.download', $uploadedDoc) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-download"></i>
                                            </a>
                                        @endif
                                    </div>
                                </li>
                            @empty
                                <li class="text-muted small">Belum ada persyaratan dokumen yang ditetapkan Admin.</li>
                            @endforelse
                        </ul>

                        {{-- Semua dokumen terunggah (termasuk "Lainnya") --}}
                        @php($others = $application->documents->whereNotIn('document_type', $requirements->pluck('document_name')))
                        @if ($others->isNotEmpty())
                            <hr>
                            <h6 class="fw-semibold mb-3">Dokumen Tambahan</h6>
                            <ul class="list-unstyled">
                                @foreach ($others as $document)
                                    <li class="border rounded-3 p-2 mb-2 d-flex justify-content-between align-items-center">
                                        <div class="small">
                                            <strong>{{ $document->document_type }}</strong>
                                            <div class="text-muted">{{ $document->file_name }} · v{{ $document->version }} · {{ $document->sizeHuman() }}</div>
                                        </div>
                                        @if (\App\Services\AccessService::canAccessDocuments($user, $application))
                                            <a href="{{ route('documents.download', $document) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-download"></i></a>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    {{-- ============ TAB 3: RIWAYAT ============ --}}
                    <div class="tab-pane fade" id="tabHistory" role="tabpanel">
                        <ul class="timeline mb-0">
                            @forelse ($application->histories as $history)
                                <li class="timeline-item">
                                    <div class="fw-semibold small">{{ $history->action }}</div>
                                    <div class="text-muted" style="font-size:.78rem">
                                        {{ $history->user?->name ?? 'Sistem' }} · {{ $history->created_at->format('d M Y H:i') }}
                                        @if ($history->old_status || $history->new_status)
                                            · {{ \App\Enums\ApplicationStatus::tryFrom($history->old_status ?? '')?->label() ?? $history->old_status ?? '–' }}
                                            → {{ \App\Enums\ApplicationStatus::tryFrom($history->new_status ?? '')?->label() ?? $history->new_status ?? '–' }}
                                        @endif
                                    </div>
                                    @if ($history->notes)<div class="small mt-1">{{ $history->notes }}</div>@endif
                                </li>
                            @empty
                                <li class="text-muted small">Belum ada riwayat.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Panel aksi Legal/Admin --}}
        @if ($user->canReview() && in_array($application->status, [
                \App\Enums\ApplicationStatus::SUBMITTED,
                \App\Enums\ApplicationStatus::RESUBMITTED,
                \App\Enums\ApplicationStatus::UNDER_REVIEW,
            ]))
            <div class="card lf-card mb-3 border-primary">
                <div class="card-header bg-primary text-white py-3 fw-semibold"><i class="bi bi-gavel me-2"></i>Aksi Review</div>
                <div class="card-body">
                    @if (in_array($application->status, [\App\Enums\ApplicationStatus::SUBMITTED, \App\Enums\ApplicationStatus::RESUBMITTED]))
                        <form method="POST" action="{{ route('review.start', $application) }}">
                            @csrf
                            <button class="btn btn-primary w-100 mb-2">
                                <i class="bi bi-search me-1"></i>Mulai Review
                            </button>
                        </form>
                        <p class="text-muted small mb-0">Ambil pengajuan ini untuk mulai diperiksa (status menjadi <strong>Sedang Direview</strong>).</p>
                    @elseif ($application->status === \App\Enums\ApplicationStatus::UNDER_REVIEW)
                        <form method="POST" action="{{ route('review.decide', $application) }}">
                            @csrf
                            <label class="form-label small fw-semibold">Keputusan</label>
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="actApprove" value="approve" checked
                                           onchange="toggleValidUntil()">
                                    <label class="form-check-label" for="actApprove"><i class="bi bi-check-circle text-success me-1"></i>Setujui / Terbitkan</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="actRevision" value="revision"
                                           onchange="toggleValidUntil()">
                                    <label class="form-check-label" for="actRevision"><i class="bi bi-arrow-repeat text-warning me-1"></i>Minta Revisi</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="action" id="actReject" value="reject"
                                           onchange="toggleValidUntil()">
                                    <label class="form-check-label" for="actReject"><i class="bi bi-x-circle text-danger me-1"></i>Tolak</label>
                                </div>
                            </div>
                            <div class="mb-3" id="validUntilWrap">
                                <label class="form-label small fw-semibold">Masa Berlaku Dokumen</label>
                                <input type="date" name="valid_until" id="validUntilInput"
                                       value="{{ $application->application_type === \App\Enums\ApplicationType::AGREEMENT
                                           ? $application->fieldValue('tanggal_selesai')
                                           : '' }}"
                                       class="form-control form-control-sm">
                                <div class="form-text">
                                    @if ($application->application_type === \App\Enums\ApplicationType::AGREEMENT)
                                        Otomatis dari tanggal berakhir kontrak (bisa diubah).
                                    @else
                                        Opsional — kosongkan bila izin berlaku tanpa batas waktu.
                                    @endif
                                </div>
                            </div>
                            <label class="form-label small fw-semibold">Catatan <span class="text-danger">(wajib untuk revisi/tolak)</span></label>
                            <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                                      placeholder="Contoh: Dokumen tidak memenuhi persyaratan..."></textarea>
                            @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @error('valid_until')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
                            <button class="btn btn-primary w-100 mt-3"><i class="bi bi-send-check me-1"></i>Kirim Keputusan</button>
                        </form>
                    @endif
                </div>
            </div>
        @endif

        {{-- Ajukan / Ajukan ulang --}}
        @if ($application->isOwnedBy($user) && $application->isEditable())
            <div class="card lf-card mb-3 border-success">
                <div class="card-body">
                    <form method="POST" action="{{ route('applications.submit', $application) }}">
                        @csrf
                        <button class="btn btn-success w-100">
                            <i class="bi bi-send me-1"></i>
                            {{ $application->status === \App\Enums\ApplicationStatus::REVISION_REQUESTED ? 'Ajukan Ulang ke Legal' : 'Ajukan ke Legal' }}
                        </button>
                    </form>
                    <p class="text-muted small mt-2 mb-0">
                        <i class="bi bi-info-circle me-1"></i> Pastikan seluruh field wajib &amp; dokumen wajib telah terisi.
                    </p>
                </div>
            </div>
        @endif

        {{-- Minta akses (user lain) --}}
        @if (! $application->isOwnedBy($user) && ! $user->canReview() && $application->status === \App\Enums\ApplicationStatus::APPROVED)
            @if ($myAccess)
                <div class="card lf-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-semibold small">Permintaan Akses Anda</span>
                            <span class="badge badge-status bg-{{ $myAccess->status->color() }}">{{ $myAccess->status->label() }}</span>
                        </div>
                        @if ($myAccess->status === \App\Enums\AccessStatus::APPROVED)
                            <div class="small text-muted mt-1">
                                Akses {{ $myAccess->access_type }} s/d {{ $myAccess->expired_at?->format('d M Y') }}
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="card lf-card mb-3 border-warning">
                    <div class="card-header bg-warning py-3 fw-semibold"><i class="bi bi-key me-2"></i>Minta Akses Dokumen</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('access-requests.store', $application) }}">
                            @csrf
                            <label class="form-label small fw-semibold">Alasan Permintaan <span class="text-danger">*</span></label>
                            <textarea name="reason" rows="3" class="form-control @error('reason') is-invalid @enderror"
                                      placeholder="Contoh: Dokumen diperlukan untuk proses review kontrak dengan supplier."></textarea>
                            @error('reason')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <button class="btn btn-warning w-100 mt-3 text-dark"><i class="bi bi-send me-1"></i>Kirim Permintaan</button>
                        </form>
                    </div>
                </div>
            @endif
        @endif

        {{-- Permintaan akses untuk pengajuan ini (pemilik & legal/admin) --}}
        @if (($application->isOwnedBy($user) || $user->canReview()) && $application->accessRequests->isNotEmpty())
            <div class="card lf-card mb-3">
                <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-person-lock me-2"></i>Permintaan Akses</div>
                <ul class="list-group list-group-flush">
                    @foreach ($application->accessRequests as $accessRequest)
                        <li class="list-group-item small">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $accessRequest->requester->name }}</strong>
                                <span class="badge badge-status bg-{{ $accessRequest->status->color() }}">{{ $accessRequest->status->label() }}</span>
                            </div>
                            <div class="text-muted mt-1">"{{ $accessRequest->reason }}"</div>
                            @if ($accessRequest->status === \App\Enums\AccessStatus::APPROVED && $accessRequest->expired_at)
                                <div class="text-muted">Akses {{ $accessRequest->access_type }} s/d {{ $accessRequest->expired_at->format('d M Y') }}</div>
                            @endif

                            @if ($user->canReview() && $accessRequest->status === \App\Enums\AccessStatus::REQUESTED)
                                <form method="POST" action="{{ route('access-requests.approve', $accessRequest) }}" class="row g-2 mt-2 align-items-end">
                                    @csrf
                                    <div class="col-5">
                                        <select name="access_type" class="form-select form-select-sm">
                                            <option value="VIEW">View Only</option>
                                            <option value="DOWNLOAD">View + Download</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <input type="date" name="expired_at" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-3">
                                        <button class="btn btn-sm btn-success w-100">Setujui</button>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Catatan (misal: masa akses terbatas 30 hari)">
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('access-requests.reject', $accessRequest) }}" class="mt-2">
                                    @csrf
                                    <div class="input-group input-group-sm">
                                        <input type="text" name="review_notes" class="form-control" placeholder="Alasan penolakan (wajib)..." required>
                                        <button class="btn btn-outline-danger">Tolak</button>
                                    </div>
                                </form>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
    function toggleValidUntil() {
        const approve = document.getElementById('actApprove');
        const wrap = document.getElementById('validUntilWrap');
        if (approve && wrap) {
            wrap.style.display = approve.checked ? '' : 'none';
        }
    }
    toggleValidUntil();
</script>
@endpush

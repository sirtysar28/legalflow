@extends('layouts.app')
@section('title', $isCreate ? 'Buat Pengajuan' : 'Edit Pengajuan')

@section('content')
@php($isPermit = $application->application_type === \App\Enums\ApplicationType::PERMIT)
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">
            @if ($isCreate)
                {{ $isPermit ? 'Buat Pengajuan Izin' : 'Buat Agreement Baru' }}
            @else
                Edit Pengajuan <code>{{ $application->application_number }}</code>
            @endif
        </h4>
        <p class="text-muted mb-0">
            Status saat ini:
            <span class="badge badge-status bg-{{ $application->statusColor() }}">{{ $application->statusLabel() }}</span>
        </p>
    </div>
    <a href="{{ route('applications.index', ['type' => $application->application_type->value]) }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

@if (! $isCreate && $application->status === \App\Enums\ApplicationStatus::REVISION_REQUESTED)
    @php($latestRevision = $application->reviews->firstWhere('action', \App\Models\ApplicationReview::ACTION_REVISION))
    @if ($latestRevision)
        <div class="alert alert-warning">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-circle me-1"></i>Catatan Revisi dari Legal/Admin:</div>
            {{ $latestRevision->notes }}
        </div>
    @endif
@endif

<div class="row g-3">
    <div class="col-lg-8">
        {{-- ================= STEPPER MULTI-STEP ================= --}}
        <div class="card lf-card mb-3">
            <div class="card-body py-3">
                @php($wizardSteps = $isPermit ? [
                    ['icon' => 'bi-list-check',   'label' => 'Jenis Izin',        'desc' => 'Pilih kategori & jenis izin'],
                    ['icon' => 'bi-journal-text', 'label' => 'Informasi Umum',   'desc' => 'Judul, deskripsi & detail'],
                    ['icon' => 'bi-cloud-arrow-up','label' => 'Dokumen',          'desc' => 'Cek persyaratan & unggah'],
                ] : [
                    ['icon' => 'bi-truck',        'label' => 'Supplier',         'desc' => 'Pilih counterparty & assessment'],
                    ['icon' => 'bi-journal-text', 'label' => 'Informasi & Detail','desc' => 'Judul, nilai & tanggal'],
                    ['icon' => 'bi-cloud-arrow-up','label' => 'Dokumen',          'desc' => 'Cek persyaratan & unggah'],
                ])
                <div class="lf-stepper" id="lfStepper">
                    @foreach ($wizardSteps as $i => $step)
                        <button type="button" class="lf-step-btn {{ $i === 0 ? 'active' : '' }}" data-goto="{{ $i }}">
                            <span class="lf-step-num">{{ $i + 1 }}</span>
                            <span class="lf-step-text d-none d-sm-block">
                                <span class="lf-step-title"><i class="bi {{ $step['icon'] }} me-1"></i>{{ $step['label'] }}</span>
                                <span class="lf-step-desc">{{ $step['desc'] }}</span>
                            </span>
                        </button>
                        @if (! $loop->last)<span class="lf-step-sep"></span>@endif
                    @endforeach
                </div>
                <div class="form-text mt-2 mb-0 text-center" id="wizardHint">
                    Langkah 1 dari 3 — lengkapi lalu tekan <strong>Lanjut</strong>.
                </div>
            </div>
        </div>

        <form method="POST"
              action="{{ $isCreate ? route('applications.store') : route('applications.update', $application) }}"
              enctype="multipart/form-data" id="applicationForm">
            @csrf
            @if (! $isCreate) @method('PUT') @endif
            <input type="hidden" name="application_type" value="{{ $application->application_type->value }}">

            {{-- ================= LANGKAH 1 ================= --}}
            <div class="wizard-pane active" data-wizard-step="0">
                <div class="card lf-card mb-3">
                    <div class="card-header bg-white py-3 fw-semibold">
                        <i class="bi bi-1-circle me-2"></i>
                        {{ $isPermit ? 'Pilih Jenis Izin' : 'Supplier / Counterparty' }}
                    </div>
                    <div class="card-body">
                        @if ($isPermit)
                            <div class="mb-0">
                                <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                                <select name="permit_type_id" id="permitTypeSelect" class="form-select @error('permit_type_id') is-invalid @enderror" required>
                                    <option value="">— Pilih Jenis Izin —</option>
                                    @php($grouped = $permitTypes->groupBy(fn ($t) => $t->category?->label() ?? 'Tanpa Kategori'))
                                    @foreach ($grouped as $categoryLabel => $group)
                                        <optgroup label="{{ $categoryLabel }}">
                                            @foreach ($group as $permitType)
                                                <option value="{{ $permitType->id }}"
                                                    {{ old('permit_type_id', $application->permit_type_id) == $permitType->id ? 'selected' : '' }}>
                                                    {{ $permitType->name }}
                                                </option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                                @error('permit_type_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                <div class="form-text">Dikelompokkan per kategori: Usaha, Bangunan, Lingkungan, Produk, Operasional. Setiap jenis izin memiliki persyaratan dokumen yang berbeda.</div>
                            </div>
                        @else
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Supplier <span class="text-danger">*</span></label>
                                    <select name="supplier_id" id="supplierSelect" class="form-select @error('supplier_id') is-invalid @enderror" required>
                                        <option value="">— Pilih Supplier —</option>
                                        @foreach ($suppliers as $supplier)
                                            @php($summary = $supplier->assessmentSummary())
                                            <option value="{{ $supplier->id }}"
                                                    data-passed="{{ $summary['passed'] ? '1' : '0' }}"
                                                    data-info="{{ $supplier->is_registered ? 'Terdaftar' : 'Belum terdaftar' }} • Risk: {{ $summary['risk'] }} • Score: {{ $summary['score'] ?? '-' }} • {{ $supplier->data_complete ? 'Data lengkap' : 'Data belum lengkap' }} • {{ $supplier->documents_complete ? 'Dokumen lengkap' : 'Dokumen belum lengkap' }}"
                                                    {{ old('supplier_id', $application->supplier_id) == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('supplier_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 d-flex align-items-end">
                                    <div id="supplierInfo" class="alert small w-100 py-2 px-3 mb-0" style="display:none;"></div>
                                </div>
                            </div>
                            <div class="form-text mt-2">Data Supplier Assessment System (Status, Risk, Score) otomatis tampil di atas dan saat direview Legal.</div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= LANGKAH 2 ================= --}}
            <div class="wizard-pane" data-wizard-step="1">
                <div class="card lf-card mb-3">
                    <div class="card-header bg-white py-3 fw-semibold">
                        <i class="bi bi-2-circle me-2"></i>Informasi Umum Pengajuan
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Judul Pengajuan <span class="text-danger">*</span></label>
                            <input type="text" name="title" value="{{ old('title', $application->title) }}"
                                   class="form-control @error('title') is-invalid @enderror" required
                                   placeholder="{{ $isPermit ? 'Contoh: Permohonan Izin Usaha Kantor Cabang' : 'Contoh: Agreement Pengadaan Bahan Baku 2026' }}">
                            @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-0">
                            <label class="form-label">Deskripsi / Ringkasan</label>
                            <textarea name="description" rows="3" class="form-control"
                                      placeholder="Jelaskan secara singkat pengajuan ini">{{ old('description', $application->description) }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="card lf-card">
                    <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-journal-plus me-2"></i>Detail Pengajuan</div>
                    <div class="card-body">
                        <div class="row g-3">
                            @foreach ($fields as $field)
                                <div class="{{ $field['type'] === 'textarea' ? 'col-12' : 'col-md-6' }}">
                                    <label class="form-label">{{ $field['label'] }} @if($field['required'])<span class="text-danger">*</span>@endif</label>
                                    @if ($field['type'] === 'textarea')
                                        <textarea name="fields[{{ $field['name'] }}]" rows="3" class="form-control">{{ old('fields.'.$field['name'], $application->fieldValue($field['name'])) }}</textarea>
                                    @elseif ($field['type'] === 'select')
                                        <select name="fields[{{ $field['name'] }}]" class="form-select">
                                            <option value="">— Pilih —</option>
                                            @foreach ($field['options'] as $optValue => $optLabel)
                                                <option value="{{ $optValue }}"
                                                    {{ old('fields.'.$field['name'], $application->fieldValue($field['name'])) === $optValue ? 'selected' : '' }}>
                                                    {{ $optLabel }}
                                                </option>
                                            @endforeach
                                        </select>
                                    @else
                                        <input type="{{ $field['type'] }}" name="fields[{{ $field['name'] }}]"
                                               value="{{ old('fields.'.$field['name'], $application->fieldValue($field['name'])) }}" class="form-control">
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================= LANGKAH 3 ================= --}}
            <div class="wizard-pane" data-wizard-step="2">
                <div class="card lf-card">
                    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                        <span class="fw-semibold"><i class="bi bi-3-circle me-2"></i>Persyaratan Dokumen</span>
                        @if (! $isCreate)
                            @php($progress = $application->documentProgress())
                            <span class="small text-muted">{{ $progress['uploaded'] }}/{{ $progress['total'] }} wajib terunggah</span>
                        @endif
                    </div>
                    <div class="card-body">
                        @if ($isPermit && ! $application->permit_type_id && $isCreate)
                            <p class="text-muted mb-0"><i class="bi bi-info-circle me-1"></i>Pilih jenis izin pada Langkah 1 terlebih dahulu untuk melihat persyaratan dokumen.</p>
                        @else
                            <ul class="list-unstyled mb-0" id="requirementList">
                                @foreach ($requirements as $requirement)
                                    <li class="mb-2 requirement-item"
                                        data-permit="{{ $requirement->permit_type_id ?? 'any' }}"
                                        data-required="{{ $requirement->is_required ? '1' : '0' }}"
                                        style="{{ $isPermit && $requirement->permit_type_id && $requirement->permit_type_id != old('permit_type_id', $application->permit_type_id) ? 'display:none' : '' }}">
                                        <i class="bi bi-file-earmark me-2"></i>{{ $requirement->document_name }}
                                        @if ($requirement->is_required)
                                            <span class="badge bg-danger badge-status">Wajib</span>
                                        @else
                                            <span class="badge bg-secondary badge-status">Opsional</span>
                                        @endif
                                        @if (! $isCreate)
                                            @php($uploadedDoc = $application->documents->firstWhere('document_type', $requirement->document_name))
                                            @if ($uploadedDoc)
                                                <i class="bi bi-check-circle-fill req-done ms-2" title="Sudah diunggah"></i>
                                                <span class="small text-muted">({{ $uploadedDoc->file_name }} v{{ $uploadedDoc->version }})</span>
                                            @elseif ($requirement->is_required)
                                                <i class="bi bi-x-circle-fill req-missing ms-2" title="Belum diunggah"></i>
                                            @endif
                                        @endif
                                    </li>
                                @endforeach
                                @if ($requirements->isEmpty())
                                    <li class="text-muted">Belum ada persyaratan dokumen yang ditetapkan Admin.</li>
                                @endif
                            </ul>
                        @endif

                        @if ($isCreate)
                            <div class="alert alert-info py-2 px-3 small mt-3 mb-0">
                                <i class="bi bi-info-circle me-1"></i>
                                Tekan <strong>Simpan Draft</strong> terlebih dahulu — setelah draft tersimpan, halaman
                                upload dokumen (dengan progress bar kelengkapan) akan tersedia di panel kanan.
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ================= NAVIGASI WIZARD ================= --}}
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3">
                <button type="button" class="btn btn-outline-secondary" id="wizardPrev" disabled>
                    <i class="bi bi-arrow-left me-1"></i>Sebelumnya
                </button>
                <div class="d-flex flex-wrap gap-2">
                    <button type="button" class="btn btn-primary" id="wizardNext">
                        Lanjut <i class="bi bi-arrow-right ms-1"></i>
                    </button>
                    <span class="d-none d-flex flex-wrap gap-2" id="wizardActions">
                        <button type="submit" class="btn btn-outline-primary">
                            <i class="bi bi-save me-1"></i> Simpan {{ $isCreate ? 'Draft' : 'Perubahan' }}
                        </button>
                        @if (! $isCreate && $application->isEditable())
                            <button type="submit" formaction="{{ route('applications.submit', $application) }}"
                                    formnovalidate class="btn btn-success">
                                <i class="bi bi-send me-1"></i>
                                {{ $application->status === \App\Enums\ApplicationStatus::REVISION_REQUESTED ? 'Ajukan Ulang ke Legal' : 'Ajukan ke Legal' }}
                            </button>
                        @endif
                    </span>
                </div>
            </div>
        </form>
    </div>

    @if (! $isCreate)
        <div class="col-lg-4">
            <div class="card lf-card sticky-top" style="top: 90px;">
                <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-upload me-2"></i>Upload Dokumen</div>
                <div class="card-body">
                    {{-- Progress bar kelengkapan dokumen wajib --}}
                    @php($progress = $application->documentProgress())
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold">Kelengkapan Dokumen</span>
                            <span id="progressLabel">{{ $progress['uploaded'] }}/{{ $progress['total'] }} ({{ $progress['percent'] }}%)</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div id="progressBar" class="progress-bar {{ $progress['percent'] >= 100 ? 'bg-success' : 'bg-primary' }}"
                                 role="progressbar" style="width: {{ $progress['percent'] }}%"
                                 aria-valuenow="{{ $progress['percent'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                    @if ($application->isEditable())
                        <form method="POST" action="{{ route('documents.store', $application) }}" enctype="multipart/form-data" class="mb-3">
                            @csrf
                            <label class="form-label small fw-semibold">Jenis Dokumen</label>
                            <select name="document_type" class="form-select form-select-sm mb-2" required>
                                <option value="">— Pilih —</option>
                                @foreach ($requirements as $requirement)
                                    <option value="{{ $requirement->document_name }}">{{ $requirement->document_name }}</option>
                                @endforeach
                                <option value="Lainnya">Lainnya</option>
                            </select>
                            <label class="form-label small fw-semibold">File (maks 10MB)</label>
                            <input type="file" name="file" class="form-control form-control-sm mb-3" required
                                   accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png">
                            <button class="btn btn-primary btn-sm w-100"><i class="bi bi-cloud-arrow-up me-1"></i>Upload</button>
                        </form>
                    @else
                        <div class="alert alert-info py-2 small mb-0">Dokumen hanya dapat diunggah saat status <strong>Draft</strong> atau <strong>Perlu Revisi</strong>.</div>
                    @endif

                    <hr>
                    <h6 class="fw-semibold small mb-2">Dokumen Terunggah ({{ $application->documents->count() }})</h6>
                    <ul class="list-unstyled mb-0 small">
                        @forelse ($application->documents as $document)
                            <li class="border rounded-3 p-2 mb-2">
                                <div class="d-flex justify-content-between align-items-start gap-2">
                                    <div>
                                        <div class="fw-semibold">{{ $document->document_type }}</div>
                                        <div class="text-muted text-break">{{ $document->file_name }}</div>
                                        <span class="badge bg-light text-dark">v{{ $document->version }}</span>
                                        <span class="badge bg-{{ $document->status === 'ISSUED' ? 'success' : 'secondary' }}">{{ $document->status }}</span>
                                    </div>
                                    @if ($application->isEditable())
                                        <form method="POST" action="{{ route('documents.destroy', [$application, $document]) }}"
                                              onsubmit="return confirm('Hapus dokumen ini?')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                        </form>
                                    @endif
                                </div>
                            </li>
                        @empty
                            <li class="text-muted">Belum ada dokumen.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<style>
    /* ================= STEPPER WIZARD ================= */
    .lf-stepper { display: flex; align-items: center; gap: .5rem; }
    .lf-step-btn {
        display: flex; align-items: center; gap: .65rem; border: none; background: transparent;
        padding: .45rem .6rem; border-radius: .75rem; text-align: left; white-space: nowrap;
        transition: background .15s ease;
    }
    .lf-step-btn:hover { background: #f1f4fa; }
    .lf-step-num {
        width: 34px; height: 34px; border-radius: 50%; flex-shrink: 0;
        display: inline-flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: .9rem; background: #e2e8f2; color: #64748b;
        transition: all .2s ease;
    }
    .lf-step-title { display: block; font-weight: 700; font-size: .84rem; color: #26355c; line-height: 1.25; }
    .lf-step-desc { display: block; font-size: .7rem; color: #8a94a6; }
    .lf-step-btn.active .lf-step-num {
        background: linear-gradient(135deg, var(--lf-navy-2), var(--lf-accent));
        color: #fff; box-shadow: 0 4px 12px rgba(45, 93, 168, .4);
    }
    .lf-step-btn.active .lf-step-title { color: var(--lf-accent); }
    .lf-step-btn.done .lf-step-num { background: #16a34a; color: #fff; }
    .lf-step-sep { flex: 1 1 24px; min-width: 20px; max-width: 80px; height: 2px; background: #dde3ee; border-radius: 99px; }

    .wizard-pane { display: none; animation: wizardFade .25s ease; }
    .wizard-pane.active { display: block; }
    @keyframes wizardFade {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    @media (max-width: 575.98px) {
        .lf-stepper { justify-content: center; }
        .lf-step-sep { flex: 1 1 14px; min-width: 14px; }
    }
</style>

<script>
    // ================= WIZARD MULTI-STEP =================
    (function () {
        const panes = Array.from(document.querySelectorAll('.wizard-pane'));
        const stepperBtns = Array.from(document.querySelectorAll('.lf-step-btn'));
        const btnPrev = document.getElementById('wizardPrev');
        const btnNext = document.getElementById('wizardNext');
        const actions = document.getElementById('wizardActions');
        const hint = document.getElementById('wizardHint');
        const hints = [
            'Langkah 1 dari 3 — pilih {{ $isPermit ? "jenis izin" : "supplier / counterparty" }} lalu tekan Lanjut.',
            'Langkah 2 dari 3 — lengkapi informasi umum & detail pengajuan.',
            'Langkah 3 dari 3 — periksa persyaratan dokumen, lalu Simpan Draft / Ajukan.',
        ];
        let current = 0;

        function paneValid(i) {
            let ok = true;
            panes[i].querySelectorAll('input, select, textarea').forEach(function (el) {
                if (!el.checkValidity()) { ok = false; el.reportValidity(); }
            });
            return ok;
        }

        function show(i) {
            current = Math.max(0, Math.min(panes.length - 1, i));
            panes.forEach(function (p, idx) { p.classList.toggle('active', idx === current); });
            stepperBtns.forEach(function (b, idx) {
                b.classList.toggle('active', idx === current);
                b.classList.toggle('done', idx < current);
                const num = b.querySelector('.lf-step-num');
                num.innerHTML = idx < current ? '<i class="bi bi-check-lg"></i>' : String(idx + 1);
            });
            btnPrev.disabled = current === 0;
            btnNext.classList.toggle('d-none', current === panes.length - 1);
            actions.classList.toggle('d-none', current !== panes.length - 1);
            actions.classList.toggle('d-flex', current === panes.length - 1);
            if (hint) hint.innerHTML = hints[current] || '';
            document.getElementById('lfStepper').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        btnNext.addEventListener('click', function () {
            if (paneValid(current)) show(current + 1);
        });
        btnPrev.addEventListener('click', function () { show(current - 1); });
        stepperBtns.forEach(function (b) {
            b.addEventListener('click', function () {
                const target = parseInt(this.dataset.goto, 10);
                if (target === current) return;
                if (target < current) { show(target); return; }
                // Melompat maju: validasi setiap langkah yang dilewati.
                for (let i = current; i < target; i++) {
                    if (!paneValid(i)) { show(i); return; }
                }
                show(target);
            });
        });

        // Bila ada error validasi server (input is-invalid), buka langkah pertama yang bermasalah.
        let startStep = 0;
        for (let i = 0; i < panes.length; i++) {
            if (panes[i].querySelector('.is-invalid')) { startStep = i; break; }
        }
        show(startStep);
    })();

    // Filter persyaratan dokumen berdasarkan jenis izin yang dipilih
    const permitSelect = document.getElementById('permitTypeSelect');
    if (permitSelect) {
        permitSelect.addEventListener('change', function () {
            const value = this.value;
            document.querySelectorAll('.requirement-item').forEach(function (item) {
                const permit = item.dataset.permit;
                item.style.display = (permit === 'any' || !permit || !value || permit === value) ? '' : 'none';
            });
        });
    }

    // Panel Check Supplier Assessment (Status, Risk, Score)
    const supplierSelect = document.getElementById('supplierSelect');
    const supplierInfo = document.getElementById('supplierInfo');
    function renderSupplierInfo() {
        if (!supplierSelect || !supplierSelect.value) { supplierInfo.style.display = 'none'; return; }
        const option = supplierSelect.options[supplierSelect.selectedIndex];
        const passed = option.dataset.passed === '1';
        supplierInfo.className = 'alert small w-100 py-2 px-3 mb-0 ' + (passed ? 'alert-success' : 'alert-warning');
        supplierInfo.innerHTML =
            '<strong>' + (passed ? '<i class="bi bi-check-circle"></i> Lolos Assessment' : '<i class="bi bi-exclamation-triangle"></i> Belum Lolos Assessment') + '</strong><br>' +
            option.dataset.info;
        supplierInfo.style.display = 'block';
    }
    if (supplierSelect) {
        supplierSelect.addEventListener('change', renderSupplierInfo);
        renderSupplierInfo();
    }

    // Hitung ulang progress bar kelengkapan dokumen wajib
    function updateProgress() {
        const items = document.querySelectorAll('.requirement-item:not([style*="display:none"])');
        let total = 0, done = 0;
        items.forEach(function (item) {
            if (item.dataset.required === '1') {
                total++;
                if (item.querySelector('.req-done')) done++;
            }
        });
        const percent = total > 0 ? Math.round(done / total * 100) : 0;
        const bar = document.getElementById('progressBar');
        const label = document.getElementById('progressLabel');
        if (bar && label) {
            bar.style.width = percent + '%';
            bar.setAttribute('aria-valuenow', percent);
            bar.className = 'progress-bar ' + (percent >= 100 ? 'bg-success' : 'bg-primary');
            label.textContent = done + '/' + total + ' (' + percent + '%)';
        }
    }
    updateProgress();
</script>
@endpush

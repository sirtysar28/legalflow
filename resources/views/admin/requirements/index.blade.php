@extends('layouts.app')
@section('title', 'Persyaratan Dokumen')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Kelola Persyaratan Dokumen</h4>
    <p class="text-muted mb-0">Tentukan dokumen wajib/opsional per jenis izin (Permit) dan untuk Agreement</p>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card lf-card">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi {{ $editing ? 'bi-pencil' : 'bi-plus-circle' }} me-2"></i>{{ $editing ? 'Edit Persyaratan' : 'Tambah Persyaratan' }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $editing ? route('admin.requirements.update', $editing) : route('admin.requirements.store') }}">
                    @csrf
                    @if ($editing) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">Jenis Pengajuan <span class="text-danger">*</span></label>
                        <select name="application_type" id="reqType" class="form-select" required>
                            <option value="PERMIT" {{ old('application_type', $editing?->application_type) === 'PERMIT' ? 'selected' : '' }}>Pengajuan Izin (Permit)</option>
                            <option value="AGREEMENT" {{ old('application_type', $editing?->application_type) === 'AGREEMENT' ? 'selected' : '' }}>Agreement</option>
                        </select>
                    </div>
                    <div class="mb-3" id="permitTypeWrapper">
                        <label class="form-label">Jenis Izin <span class="text-danger">*</span></label>
                        <select name="permit_type_id" class="form-select">
                            <option value="">— Berlaku semua jenis izin —</option>
                            @foreach ($permitTypes as $permitType)
                                <option value="{{ $permitType->id }}" {{ old('permit_type_id', $editing?->permit_type_id) == $permitType->id ? 'selected' : '' }}>{{ $permitType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama Dokumen <span class="text-danger">*</span></label>
                        <input type="text" name="document_name" value="{{ old('document_name', $editing?->document_name) }}" class="form-control"
                               placeholder="Contoh: Surat Permohonan" required>
                    </div>
                    <div class="form-check form-switch mb-2">
                        <input class="form-check-input" type="checkbox" name="is_required" value="1" id="reqSwitch"
                               {{ old('is_required', $editing?->is_required ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="reqSwitch">Wajib diunggah</label>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch"
                               {{ old('is_active', $editing?->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activeSwitch">Aktif</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        @if ($editing)
                            <a href="{{ route('admin.requirements.index') }}" class="btn btn-outline-secondary">Batal</a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card lf-card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Dokumen</th><th>Jenis Pengajuan</th><th>Jenis Izin</th><th>Wajib</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($requirements as $requirement)
                            <tr>
                                <td>{{ $requirement->document_name }}</td>
                                <td><span class="badge bg-light text-dark">{{ $requirement->application_type }}</span></td>
                                <td class="small">{{ $requirement->permitType?->name ?? 'Semua / Agreement' }}</td>
                                <td>
                                    @if ($requirement->is_required)<span class="badge bg-danger">Wajib</span>
                                    @else<span class="badge bg-secondary">Opsional</span>@endif
                                </td>
                                <td><span class="badge bg-{{ $requirement->is_active ? 'success' : 'secondary' }}">{{ $requirement->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.requirements.index', ['edit' => $requirement->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.requirements.destroy', $requirement) }}" class="d-inline"
                                          onsubmit="return confirm('Hapus persyaratan ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada persyaratan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const reqType = document.getElementById('reqType');
    const wrapper = document.getElementById('permitTypeWrapper');
    function togglePermitType() {
        wrapper.style.display = reqType.value === 'PERMIT' ? '' : 'none';
    }
    reqType.addEventListener('change', togglePermitType);
    togglePermitType();
</script>
@endpush

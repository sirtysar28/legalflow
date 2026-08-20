@extends('layouts.app')
@section('title', 'Jenis Izin')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Kelola Jenis Izin</h4>
    <p class="text-muted mb-0">Setiap jenis izin dapat memiliki persyaratan dokumen yang berbeda</p>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card lf-card">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi {{ $editing ? 'bi-pencil' : 'bi-plus-circle' }} me-2"></i>{{ $editing ? 'Edit Jenis Izin' : 'Tambah Jenis Izin' }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $editing ? route('admin.permit-types.update', $editing) : route('admin.permit-types.store') }}">
                    @csrf
                    @if ($editing) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">Nama Jenis Izin <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kategori Izin <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            @foreach (\App\Enums\PermitCategory::cases() as $category)
                                <option value="{{ $category->value }}"
                                    {{ old('category', $editing?->category?->value ?? 'USAHA') === $category->value ? 'selected' : '' }}>
                                    {{ $category->label() }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Kategori: Usaha, Bangunan, Lingkungan, Produk, Operasional.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" rows="3" class="form-control">{{ old('description', $editing?->description) }}</textarea>
                    </div>
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="activeSwitch"
                               {{ old('is_active', $editing?->is_active ?? true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="activeSwitch">Aktif</label>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        @if ($editing)
                            <a href="{{ route('admin.permit-types.index') }}" class="btn btn-outline-secondary">Batal</a>
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
                        <tr><th>Nama</th><th>Kategori</th><th>Deskripsi</th><th>Persyaratan</th><th>Status</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($permitTypes as $permitType)
                            <tr>
                                <td>{{ $permitType->name }}</td>
                                <td><span class="badge bg-{{ $permitType->category?->color() ?? 'secondary' }} badge-status">{{ $permitType->categoryLabel() }}</span></td>
                                <td class="small text-muted">{{ Str::limit($permitType->description, 50) }}</td>
                                <td>{{ $permitType->requirements_count }}</td>
                                <td><span class="badge bg-{{ $permitType->is_active ? 'success' : 'secondary' }}">{{ $permitType->is_active ? 'Aktif' : 'Nonaktif' }}</span></td>
                                <td class="text-end">
                                    <a href="{{ route('admin.permit-types.index', ['edit' => $permitType->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.permit-types.destroy', $permitType) }}" class="d-inline"
                                          onsubmit="return confirm('Hapus jenis izin ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jenis izin.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

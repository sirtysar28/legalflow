@extends('layouts.app')
@section('title', 'Divisi')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Kelola Divisi</h4>
    <p class="text-muted mb-0">Struktur folder dokumen: Document Management → Divisi → Perizinan / Agreement</p>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card lf-card">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi {{ $editing ? 'bi-pencil' : 'bi-plus-circle' }} me-2"></i>{{ $editing ? 'Edit Divisi' : 'Tambah Divisi' }}
            </div>
            <div class="card-body">
                <form method="POST" action="{{ $editing ? route('admin.departments.update', $editing) : route('admin.departments.store') }}">
                    @csrf
                    @if ($editing) @method('PUT') @endif
                    <div class="mb-3">
                        <label class="form-label">Nama Divisi <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name', $editing?->name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Kode</label>
                        <input type="text" name="code" value="{{ old('code', $editing?->code) }}" class="form-control">
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary"><i class="bi bi-save me-1"></i>Simpan</button>
                        @if ($editing)
                            <a href="{{ route('admin.departments.index') }}" class="btn btn-outline-secondary">Batal</a>
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
                        <tr><th>Nama</th><th>Kode</th><th>Jumlah User</th><th class="text-end">Aksi</th></tr>
                    </thead>
                    <tbody>
                        @forelse ($departments as $department)
                            <tr>
                                <td>{{ $department->name }}</td>
                                <td>{{ $department->code ?? '-' }}</td>
                                <td>{{ $department->users_count }}</td>
                                <td class="text-end">
                                    <a href="{{ route('admin.departments.index', ['edit' => $department->id]) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                                    <form method="POST" action="{{ route('admin.departments.destroy', $department) }}" class="d-inline"
                                          onsubmit="return confirm('Hapus divisi ini?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center text-muted py-4">Belum ada divisi.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

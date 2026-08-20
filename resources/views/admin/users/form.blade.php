@extends('layouts.app')
@section('title', $user->exists ? 'Edit User' : 'Tambah User')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h4 class="fw-bold mb-0">{{ $user->exists ? 'Edit User: ' . $user->name : 'Tambah User Baru' }}</h4>
    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="card lf-card" style="max-width: 720px;">
    <div class="card-body">
        <form method="POST" action="{{ $user->exists ? route('admin.users.update', $user) : route('admin.users.store') }}">
            @csrf
            @if ($user->exists) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-control @error('name') is-invalid @enderror" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="form-control @error('email') is-invalid @enderror" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Role <span class="text-danger">*</span></label>
                    <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                        <option value="">— Pilih Role —</option>
                        @foreach ($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>{{ $role->label }}</option>
                        @endforeach
                    </select>
                    @error('role_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label">Divisi</label>
                    <select name="department_id" class="form-select">
                        <option value="">— Tanpa Divisi —</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ old('department_id', $user->department_id) == $department->id ? 'selected' : '' }}>{{ $department->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="active" {{ old('status', $user->status ?? 'active') === 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $user->status ?? 'active') === 'inactive' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Password {{ $user->exists ? '(kosongkan jika tidak diubah)' : '<span class="text-danger">*</span>' }}</label>
                    <div class="input-group">
                        <input type="password" name="password" id="passwordField" class="form-control @error('password') is-invalid @enderror" @unless($user->exists) required @endunless>
                        <button class="btn btn-outline-secondary toggle-password" type="button" data-target="#passwordField"
                                tabindex="-1" aria-label="Tampilkan / sembunyikan password">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
            </div>

            <button class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i>Simpan</button>
        </form>
    </div>
</div>
@endsection

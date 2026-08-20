@extends('layouts.app')
@php($user = auth()->user())
@section('title', 'Manajemen User')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h4 class="fw-bold mb-0">Manajemen User</h4>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary"><i class="bi bi-person-plus me-1"></i>Tambah User</a>
</div>

<div class="card lf-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Nama</th><th>Email</th><th>Role</th><th>Divisi</th><th>Status</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse ($users as $userRow)
                    <tr>
                        <td>{{ $userRow->name }}</td>
                        <td>{{ $userRow->email }}</td>
                        <td><span class="badge bg-{{ $userRow->isAdmin() ? 'danger' : ($userRow->isLegal() ? 'info' : 'secondary') }}">{{ $userRow->role?->label }}</span></td>
                        <td>{{ $userRow->department?->name ?? '-' }}</td>
                        <td><span class="badge bg-{{ $userRow->isActive() ? 'success' : 'secondary' }}">{{ $userRow->isActive() ? 'Aktif' : 'Nonaktif' }}</span></td>
                        <td class="text-end">
                            <a href="{{ route('admin.users.edit', $userRow) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                            @if ($userRow->id !== $user->id)
                                <form method="POST" action="{{ route('admin.users.destroy', $userRow) }}" class="d-inline"
                                      onsubmit="return confirm('Hapus user {{ $userRow->name }}?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada user.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">{{ $users->links() }}</div>
</div>
@endsection

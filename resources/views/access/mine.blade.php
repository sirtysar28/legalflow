@extends('layouts.app')
@section('title', 'Request Akses Saya')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Request Akses Dokumen Saya</h4>
    <p class="text-muted mb-0">Status permintaan akses dokumen milik pengguna/divisi lain</p>
</div>

<div class="card lf-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Dokumen</th><th>Pemilik / Divisi</th><th>Alasan</th><th>Status</th><th>Akses</th><th>Masa Berlaku</th><th>Catatan Admin</th></tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td>
                            <code>{{ $request->application->application_number }}</code>
                            <div class="small">{{ Str::limit($request->application->title, 30) }}</div>
                        </td>
                        <td>
                            {{ $request->application->user->name }}
                            <div class="text-muted small">{{ $request->application->department?->name }}</div>
                        </td>
                        <td class="small" style="max-width: 220px;">{{ Str::limit($request->reason, 90) }}</td>
                        <td><span class="badge badge-status bg-{{ $request->status->color() }}">{{ $request->status->label() }}</span></td>
                        <td>{{ $request->access_type ?? '-' }}</td>
                        <td class="small">
                            @if ($request->expired_at)
                                {{ $request->expired_at->format('d M Y') }}
                                @if ($request->status === \App\Enums\AccessStatus::APPROVED)
                                    <div class="text-muted">{{ $request->isActive() ? 'Masih berlaku' : 'Sudah berakhir' }}</div>
                                @endif
                            @else
                                -
                            @endif
                        </td>
                        <td class="small">{{ $request->review_notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Belum ada permintaan akses.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $requests->links() }}
    </div>
</div>
@endsection

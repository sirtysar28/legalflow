@extends('layouts.app')
@section('title', 'Audit Trail')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Audit Trail</h4>
        <p class="text-muted mb-0">Seluruh aktivitas pengajuan: perubahan status, upload dokumen, review, dll.</p>
    </div>
    <form method="GET" class="d-flex gap-2">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari aksi / nomor / judul...">
        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="card lf-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr><th>Waktu</th><th>Pengajuan</th><th>Aksi</th><th>Oleh</th><th>Transisi Status</th><th>Catatan</th></tr>
            </thead>
            <tbody>
                @forelse ($histories as $history)
                    <tr>
                        <td class="text-nowrap small">{{ $history->created_at->format('d M Y H:i') }}</td>
                        <td>
                            <code>{{ $history->application?->application_number }}</code>
                            <div class="small">{{ Str::limit($history->application?->title, 35) }}</div>
                        </td>
                        <td>{{ $history->action }}</td>
                        <td class="small">{{ $history->user?->name ?? 'Sistem' }}</td>
                        <td class="small">
                            @if ($history->old_status || $history->new_status)
                                <span class="badge bg-secondary">{{ $history->old_status ?? '–' }}</span>
                                <i class="bi bi-arrow-right"></i>
                                <span class="badge bg-primary">{{ $history->new_status ?? '–' }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td class="small text-muted">{{ Str::limit($history->notes, 60) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada aktivitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">{{ $histories->links() }}</div>
</div>
@endsection

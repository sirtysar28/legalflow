@extends('layouts.app')
@php($user = auth()->user())
@section('title', 'Dashboard Legal/Admin')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard {{ $user->isLegal() ? 'Legal' : 'Admin' }}</h4>
        <p class="text-muted mb-0">Antrean review &amp; permintaan akses dokumen</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('review.queue') }}" class="btn btn-primary">
            <i class="bi bi-clipboard-check me-1"></i> Buka Antrean Review
        </a>
        <a href="{{ route('access-requests.incoming') }}" class="btn btn-warning">
            <i class="bi bi-person-lock me-1"></i> Permintaan Akses
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-teal"><h2>{{ $stats['submitted'] }}</h2><p>Menunggu Review</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-blue"><h2>{{ $stats['review'] }}</h2><p>Sedang Direview</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-amber"><h2>{{ $stats['revision'] }}</h2><p>Revisi</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-green"><h2>{{ $stats['approved'] }}</h2><p>Disetujui</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-red"><h2>{{ $stats['rejected'] }}</h2><p>Ditolak</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-amber"><h2>{{ $stats['expiring'] }}</h2><p>Akan Kadaluarsa</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-dark"><h2>{{ $stats['expired'] }}</h2><p>Kadaluarsa</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-purple"><h2>{{ $stats['access'] }}</h2><p>Request Akses</p></div>
    </div>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card lf-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-semibold"><i class="bi bi-inbox me-2"></i>Pengajuan Menunggu Proses</span>
                <a href="{{ route('review.queue') }}" class="btn btn-sm btn-outline-primary">Lihat Antrean</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr><th>Nomor</th><th>Pemohon</th><th>Judul</th><th>Status</th><th></th></tr>
                    </thead>
                    <tbody>
                        @forelse ($queue as $application)
                            <tr>
                                <td class="text-nowrap"><code>{{ $application->application_number }}</code></td>
                                <td>
                                    {{ $application->user->name }}
                                    <div class="text-muted small">{{ $application->department?->name }}</div>
                                </td>
                                <td>{{ Str::limit($application->title, 36) }}</td>
                                <td><span class="badge badge-status bg-{{ $application->statusColor() }}">{{ $application->statusLabel() }}</span></td>
                                <td><a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-eye"></i> Review</a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Tidak ada pengajuan menunggu review.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card lf-card">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-person-lock me-2"></i>Permintaan Akses Dokumen</span>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($accessRequests as $request)
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small fw-semibold">{{ $request->requester->name }}</div>
                                <div class="text-muted" style="font-size:.78rem">
                                    {{ $request->application->application_number }} — {{ Str::limit($request->reason, 60) }}
                                </div>
                            </div>
                            <span class="badge badge-status bg-{{ $request->status->color() }}">{{ $request->status->label() }}</span>
                        </div>
                    </li>
                @empty
                    <li class="list-group-item text-muted small py-4 text-center">Tidak ada permintaan akses baru.</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>
@endsection

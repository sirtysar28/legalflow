@extends('layouts.app')
@php($user = auth()->user())
@section('title', 'Dashboard User')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Dashboard Pengajuan</h4>
        <p class="text-muted mb-0">Selamat datang, <strong>{{ $user->name }}</strong> ({{ $user->department?->name ?? 'Tanpa Divisi' }})</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('applications.create', ['type' => 'PERMIT']) }}" class="btn btn-primary">
            <i class="bi bi-file-earmark-text me-1"></i> Buat Pengajuan Izin
        </a>
        <a href="{{ route('applications.create', ['type' => 'AGREEMENT']) }}" class="btn btn-success">
            <i class="bi bi-file-earmark-richtext me-1"></i> Buat Agreement Baru
        </a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-purple"><h2>{{ $stats['total'] }}</h2><p>Total Pengajuan</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-blue"><h2>{{ $stats['draft'] }}</h2><p>Draft</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-teal"><h2>{{ $stats['waiting'] }}</h2><p>Menunggu Review</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-amber"><h2>{{ $stats['revision'] }}</h2><p>Perlu Revisi</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-green"><h2>{{ $stats['approved'] }}</h2><p>Disetujui</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-amber"><h2>{{ $stats['expiring'] }}</h2><p>Akan Kadaluarsa</p></div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="lf-stat stat-red"><h2>{{ $stats['rejected'] }}</h2><p>Ditolak</p></div>
    </div>
</div>

{{-- Izin / kontrak akan kadaluarsa --}}
@if ($expiringSoon->isNotEmpty())
    <div class="card lf-card mb-4 border-warning">
        <div class="card-header bg-warning py-3 fw-semibold text-dark">
            <i class="bi bi-hourglass-split me-2"></i>Peringatan: Izin/Kontrak Akan Kadaluarsa ({{ config('legalflow.expiring_soon_days', 30) }} hari)
        </div>
        <ul class="list-group list-group-flush">
            @foreach ($expiringSoon as $app)
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div>
                        <div class="small fw-semibold"><a href="{{ route('applications.show', $app) }}">{{ Str::limit($app->title, 50) }}</a></div>
                        <div class="text-muted" style="font-size:.75rem">
                            <code>{{ $app->application_number }}</code> · {{ $app->permitType?->name ?? $app->supplier?->name }} · berakhir {{ $app->valid_until?->format('d M Y') }}
                        </div>
                    </div>
                    <span class="badge bg-warning text-dark badge-status">{{ $app->valid_until?->diffForHumans() }}</span>
                </li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card lf-card">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <span class="fw-semibold"><i class="bi bi-clock-history me-2"></i>Pengajuan Terbaru</span>
                <a href="{{ route('applications.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Nomor</th><th>Judul</th><th>Jenis</th><th>Status</th><th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recent as $application)
                            <tr>
                                <td class="text-nowrap"><code>{{ $application->application_number }}</code></td>
                                <td>{{ Str::limit($application->title, 40) }}</td>
                                <td><span class="badge bg-light text-dark">{{ $application->typeLabel() }}</span></td>
                                <td><span class="badge badge-status bg-{{ $application->statusColor() }}">{{ $application->statusLabel() }}</span></td>
                                <td><a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pengajuan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card lf-card mb-3">
            <div class="card-header bg-white py-3">
                <span class="fw-semibold"><i class="bi bi-key me-2"></i>Request Akses Dokumen Terbaru</span>
            </div>
            <ul class="list-group list-group-flush">
                @forelse ($myAccessRequests as $request)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small fw-semibold">{{ $request->application->application_number }}</div>
                            <div class="text-muted" style="font-size:.78rem">{{ Str::limit($request->reason, 50) }}</div>
                        </div>
                        <span class="badge badge-status bg-{{ $request->status->color() }}">{{ $request->status->label() }}</span>
                    </li>
                @empty
                    <li class="list-group-item text-muted small py-4 text-center">Belum ada permintaan akses.</li>
                @endforelse
            </ul>
            <div class="card-footer bg-white text-end py-2">
                <a href="{{ route('documents.browse') }}" class="btn btn-sm btn-outline-primary">
                    <i class="bi bi-folder-check me-1"></i>Browse Dokumen
                </a>
            </div>
        </div>

        <div class="card lf-card">
            <div class="card-body">
                <h6 class="fw-semibold mb-3"><i class="bi bi-signpost-split me-2"></i>Alur Pengajuan</h6>
                <div class="d-flex flex-wrap gap-1 align-items-center small">
                    <span class="badge bg-secondary">Draft</span> <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-info">Submitted</span> <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-primary">Review</span> <i class="bi bi-arrow-right text-muted"></i>
                    <span class="badge bg-success">Approved</span>
                    <span class="text-muted">/</span>
                    <span class="badge bg-warning text-dark">Revisi</span>
                    <span class="text-muted">/</span>
                    <span class="badge bg-danger">Rejected</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')
@php($user = auth()->user())
@php($isReviewer = $user->canReview())
@section('title', 'Daftar Pengajuan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">
            @if ($type === \App\Enums\ApplicationType::PERMIT) Dashboard Perizinan — Daftar Pengajuan Izin
            @elseif ($type === \App\Enums\ApplicationType::AGREEMENT) Dashboard Purchasing — Daftar Pengajuan Agreement
            @else {{ $isReviewer ? 'Semua Pengajuan' : 'Pengajuan Saya' }}
            @endif
        </h4>
        <p class="text-muted mb-0">
            @if ($type === \App\Enums\ApplicationType::PERMIT) Pengurusan izin usaha (NIB, PBG, SLF, UKL-UPL, Sertifikasi Halal, TDG, dll)
            @elseif ($type === \App\Enums\ApplicationType::AGREEMENT) Perjanjian/kontrak lintas divisi {{ $isReviewer ? '(seluruh divisi)' : '' }}
            @else Semua jenis pengajuan {{ $isReviewer ? 'seluruh divisi' : 'milik Anda' }}
            @endif
        </p>
    </div>
    @if (! $isReviewer)
        <div class="d-flex gap-2">
            <a href="{{ route('applications.create', ['type' => 'PERMIT']) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Buat Pengurusan Izin
            </a>
            <a href="{{ route('applications.create', ['type' => 'AGREEMENT']) }}" class="btn btn-success btn-sm">
                <i class="bi bi-plus-circle me-1"></i>Buat Agreement
            </a>
        </div>
    @endif
</div>

{{-- Statistik ringkasan modul --}}
<div class="row g-3 mb-4">
    @foreach ($stats as $stat)
        <div class="col-6 col-md-3">
            <div class="lf-stat {{ $stat['class'] }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h2>{{ $stat['value'] }}</h2>
                        <p>{{ $stat['label'] }}</p>
                    </div>
                    <i class="bi {{ $stat['icon'] }} fs-3 opacity-50"></i>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-3 mb-4">
    {{-- Grafik 1: Doughnut distribusi status --}}
    <div class="col-12 col-lg-6">
        <div class="card lf-card h-100">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-pie-chart me-2"></i>Distribusi Status
            </div>
            <div class="card-body d-flex align-items-center justify-content-center" style="min-height: 250px;">
                <div style="position: relative; width: 100%; max-width: 420px;">
                    <canvas id="statusChart"></canvas>
                </div>
                @if (array_sum($chart['data']) === 0)
                    <p class="text-center text-muted small m-0">Belum ada data.</p>
                @endif
            </div>
        </div>
    </div>

    {{-- Grafik 2: Bar tren pengajuan 6 bulan --}}
    <div class="col-12 col-lg-6">
        <div class="card lf-card h-100">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-bar-chart-line me-2"></i>Tren Pengajuan (6 Bulan)
            </div>
            <div class="card-body" style="min-height: 250px; position: relative;">
                <canvas id="trendChart"></canvas>
                @if (array_sum($trend['submitted']) === 0 && array_sum($trend['approved']) === 0)
                    <p class="text-center text-muted small m-0 position-absolute top-50 start-50 translate-middle">Belum ada data.</p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Akan kadaluarsa --}}
@if ($expiringSoon && $expiringSoon->isNotEmpty())
    <div class="card lf-card mb-4 border-warning">
        <div class="card-header bg-warning py-3 fw-semibold text-dark">
            <i class="bi bi-hourglass-split me-2"></i>
            Akan Kadaluarsa ({{ config('legalflow.expiring_soon_days', 30) }} hari)
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr><th>Nomor</th><th>Judul</th><th>Referensi</th><th class="text-end">Berakhir</th></tr>
                </thead>
                <tbody>
                    @foreach ($expiringSoon as $app)
                        <tr>
                            <td class="text-nowrap"><code>{{ $app->application_number }}</code></td>
                            <td><a href="{{ route('applications.show', $app) }}">{{ Str::limit($app->title, 45) }}</a></td>
                            <td class="small text-muted">{{ $app->permitType?->name ?? $app->supplier?->name }}</td>
                            <td class="text-end text-nowrap">
                                <span class="badge bg-warning text-dark badge-status">{{ $app->valid_until?->diffForHumans() }}</span>
                                <div class="text-muted small">{{ $app->valid_until?->format('d M Y') }}</div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<div class="card lf-card">
    <div class="card-header bg-white py-3">
        {{-- Filter status --}}
        <div class="d-flex flex-wrap gap-2 align-items-center mb-2">
            <a href="{{ route('applications.index', array_filter(['type' => $type?->value, 'q' => request('q'), 'permit_type' => request('permit_type'), 'department' => request('department')])) }}"
               class="btn btn-sm {{ $status ? 'btn-outline-secondary' : 'btn-primary' }}">Semua Status</a>
            @foreach ($statuses as $value => $label)
                <a href="{{ route('applications.index', array_filter(['type' => $type?->value, 'status' => $value, 'q' => request('q'), 'permit_type' => request('permit_type'), 'department' => request('department')])) }}"
                   class="btn btn-sm {{ $status?->value === $value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
            @endforeach
        </div>

        {{-- Filter jenis izin / divisi + pencarian --}}
        <form method="GET" class="row g-2 align-items-end">
            <input type="hidden" name="type" value="{{ $type?->value }}">
            @if ($type === \App\Enums\ApplicationType::PERMIT)
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Filter Jenis Izin</label>
                    <select name="permit_type" class="form-select form-select-sm">
                        <option value="">Semua Jenis Izin</option>
                        @foreach ($permitTypes as $permitType)
                            <option value="{{ $permitType->id }}" {{ request('permit_type') == $permitType->id ? 'selected' : '' }}>
                                {{ $permitType->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            @if ($isReviewer)
                <div class="col-sm-6 col-md-3">
                    <label class="form-label small text-muted mb-1">Filter Divisi</label>
                    <select name="department" class="form-select form-select-sm">
                        <option value="">Semua Divisi</option>
                        @foreach ($departments as $department)
                            <option value="{{ $department->id }}" {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="col-sm-6 col-md-3">
                <label class="form-label small text-muted mb-1">Cari</label>
                <div class="input-group input-group-sm">
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nomor / judul...">
                    <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
                </div>
            </div>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nomor</th>
                    <th>Judul &amp; Jenis</th>
                    @if ($isReviewer)<th>Pemohon / Divisi</th>@endif
                    <th>Referensi</th>
                    <th>{{ $type === \App\Enums\ApplicationType::PERMIT ? 'Kelengkapan' : 'Nilai Kontrak' }}</th>
                    <th>Diajukan</th>
                    <th>Status</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    @php($progress = $application->documentProgress())
                    <tr>
                        <td class="text-nowrap"><code>{{ $application->application_number }}</code></td>
                        <td>
                            <div>{{ Str::limit($application->title, 40) }}</div>
                            <span class="badge bg-light text-dark">{{ $application->typeLabel() }}</span>
                        </td>
                        @if ($isReviewer)
                            <td>
                                {{ $application->user->name }}
                                <div class="text-muted small">{{ $application->department?->name }}</div>
                            </td>
                        @endif
                        <td class="small text-muted">
                            @if ($application->permit_type_id) {{ $application->permitType?->name }} @endif
                            @if ($application->supplier_id) {{ $application->supplier?->name }} @endif
                        </td>
                        <td style="min-width: 120px;">
                            @if ($type === \App\Enums\ApplicationType::AGREEMENT || $application->application_type === \App\Enums\ApplicationType::AGREEMENT)
                                {{ $application->contractSummary() ?? '-' }}
                                <div class="text-muted small">
                                    @if ($application->valid_until)
                                        s/d {{ $application->valid_until->format('d M Y') }}
                                    @endif
                                </div>
                            @else
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px; min-width: 60px;">
                                        <div class="progress-bar {{ $progress['percent'] >= 100 ? 'bg-success' : 'bg-primary' }}"
                                             style="width: {{ $progress['percent'] }}%"></div>
                                    </div>
                                    <span class="small text-nowrap">{{ $progress['percent'] }}%</span>
                                </div>
                            @endif
                        </td>
                        <td class="text-nowrap small">{{ $application->submitted_at?->format('d M Y') ?? '-' }}</td>
                        <td>
                            <span class="badge badge-status bg-{{ $application->statusColor() }}">{{ $application->statusLabel() }}</span>
                            @if ($application->status === \App\Enums\ApplicationStatus::APPROVED && $application->valid_until)
                                <div class="text-muted small">s/d {{ $application->valid_until->format('d M Y') }}</div>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="{{ $isReviewer ? 8 : 7 }}" class="text-center text-muted py-5">Belum ada pengajuan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $applications->links() }}
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    if (window.Chart) {
        const isMobile = window.matchMedia('(max-width: 767.98px)').matches;

        // ===== 1. Doughnut: distribusi status =====
        const chartEl = document.getElementById('statusChart');
        if (chartEl && @json(array_sum($chart['data'])) > 0) {
            new Chart(chartEl, {
                type: 'doughnut',
                data: {
                    labels: @json($chart['labels']),
                    datasets: [{
                        data: @json($chart['data']),
                        backgroundColor: @json($chart['colors']),
                        borderWidth: 2,
                        borderColor: '#fff',
                        hoverOffset: 8,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: isMobile ? '55%' : '62%',
                    plugins: {
                        legend: {
                            position: isMobile ? 'bottom' : 'right',
                            labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
                        }
                    }
                }
            });
            chartEl.parentElement.style.height = isMobile ? '240px' : '260px';
        } else if (chartEl) {
            chartEl.parentElement.style.height = '0';
        }

        // ===== 2. Bar: tren pengajuan 6 bulan (diajukan vs disetujui) =====
        const trendEl = document.getElementById('trendChart');
        if (trendEl && (@json(array_sum($trend['submitted'])) > 0 || @json(array_sum($trend['approved'])) > 0)) {
            new Chart(trendEl, {
                type: 'bar',
                data: {
                    labels: @json($trend['labels']),
                    datasets: [
                        {
                            label: 'Diajukan',
                            data: @json($trend['submitted']),
                            backgroundColor: 'rgba(45, 93, 168, .85)',
                            hoverBackgroundColor: '#2d5da8',
                            borderRadius: 6,
                            maxBarThickness: 32,
                        },
                        {
                            label: 'Disetujui',
                            data: @json($trend['approved']),
                            backgroundColor: 'rgba(34, 160, 77, .85)',
                            hoverBackgroundColor: '#22a04d',
                            borderRadius: 6,
                            maxBarThickness: 32,
                        },
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: isMobile ? 'bottom' : 'top',
                            align: isMobile ? 'center' : 'end',
                            labels: { boxWidth: 12, padding: 12, font: { size: 11 } }
                        }
                    },
                    scales: {
                        x: { grid: { display: false }, ticks: { font: { size: 10 } } },
                        y: { beginAtZero: true, ticks: { precision: 0, font: { size: 10 } }, grid: { color: '#eef1f7' } }
                    }
                }
            });
        }
    }
</script>
@endpush

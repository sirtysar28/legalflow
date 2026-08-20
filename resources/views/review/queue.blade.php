@extends('layouts.app')
@section('title', 'Antrean Review')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Antrean Review Legal/Admin</h4>
    <p class="text-muted mb-0">Pengajuan Perizinan &amp; Agreement yang menunggu pemeriksaan</p>
</div>

<div class="card lf-card">
    <div class="card-header bg-white py-3">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('review.queue') }}" class="btn btn-sm {{ $status ? 'btn-outline-secondary' : 'btn-primary' }}">Antrean Aktif</a>
            @foreach ($statuses as $value => $label)
                <a href="{{ route('review.queue', ['status' => $value]) }}"
                   class="btn btn-sm {{ $status?->value === $value ? 'btn-primary' : 'btn-outline-secondary' }}">{{ $label }}</a>
            @endforeach
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nomor</th><th>Judul</th><th>Jenis</th><th>Pemohon / Divisi</th>
                    <th>Diajukan</th><th>Status</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td class="text-nowrap"><code>{{ $application->application_number }}</code></td>
                        <td>{{ Str::limit($application->title, 40) }}</td>
                        <td><span class="badge bg-light text-dark">{{ $application->typeLabel() }}</span></td>
                        <td>
                            {{ $application->user->name }}
                            <div class="text-muted small">{{ $application->department?->name }}</div>
                        </td>
                        <td class="text-nowrap small">{{ $application->submitted_at?->format('d M Y H:i') ?? '-' }}</td>
                        <td><span class="badge badge-status bg-{{ $application->statusColor() }}">{{ $application->statusLabel() }}</span></td>
                        <td class="text-end">
                            @if (in_array($application->status, [\App\Enums\ApplicationStatus::SUBMITTED, \App\Enums\ApplicationStatus::RESUBMITTED]))
                                <form method="POST" action="{{ route('review.start', $application) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm btn-primary"><i class="bi bi-search me-1"></i>Mulai Review</button>
                                </form>
                            @endif
                            <a href="{{ route('applications.show', $application) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted py-5">Antrean kosong.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $applications->links() }}
    </div>
</div>
@endsection

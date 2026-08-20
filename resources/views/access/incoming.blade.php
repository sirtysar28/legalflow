@extends('layouts.app')
@section('title', 'Permintaan Akses Dokumen')

@section('content')
<div class="mb-4">
    <h4 class="fw-bold mb-1">Permintaan Akses Dokumen</h4>
    <p class="text-muted mb-0">Review permintaan akses dari user lain — setujui dengan jenis &amp; masa berlaku akses, atau tolak</p>
</div>

<div class="card lf-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr><th>Peminta</th><th>Dokumen</th><th>Alasan</th><th>Status</th><th style="min-width: 380px;">Proses</th></tr>
            </thead>
            <tbody>
                @forelse ($requests as $request)
                    <tr>
                        <td>
                            {{ $request->requester->name }}
                            <div class="text-muted small">{{ $request->requester->department?->name }}</div>
                        </td>
                        <td>
                            <code>{{ $request->application->application_number }}</code>
                            <div class="small">{{ Str::limit($request->application->title, 30) }}</div>
                            <div class="text-muted small">{{ $request->application->department?->name }}</div>
                        </td>
                        <td class="small" style="max-width: 240px;">{{ $request->reason }}</td>
                        <td>
                            <span class="badge badge-status bg-{{ $request->status->color() }}">{{ $request->status->label() }}</span>
                            @if ($request->status === \App\Enums\AccessStatus::APPROVED && $request->expired_at)
                                <div class="small text-muted mt-1">{{ $request->access_type }} s/d {{ $request->expired_at->format('d M Y') }}</div>
                            @endif
                            @if ($request->reviewer)
                                <div class="small text-muted mt-1">oleh {{ $request->reviewer->name }}</div>
                            @endif
                        </td>
                        <td>
                            @if ($request->status === \App\Enums\AccessStatus::REQUESTED)
                                <form method="POST" action="{{ route('access-requests.approve', $request) }}" class="row g-2 align-items-end">
                                    @csrf
                                    <div class="col-4">
                                        <label class="form-label small mb-1">Jenis Akses</label>
                                        <select name="access_type" class="form-select form-select-sm">
                                            <option value="VIEW">View Only</option>
                                            <option value="DOWNLOAD">View + Download</option>
                                        </select>
                                    </div>
                                    <div class="col-4">
                                        <label class="form-label small mb-1">Berlaku s/d</label>
                                        <input type="date" name="expired_at" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-4">
                                        <button class="btn btn-sm btn-success w-100"><i class="bi bi-check2 me-1"></i>Setujui</button>
                                    </div>
                                    <div class="col-12">
                                        <input type="text" name="notes" class="form-control form-control-sm" placeholder="Catatan (opsional)">
                                    </div>
                                </form>
                                <form method="POST" action="{{ route('access-requests.reject', $request) }}" class="input-group input-group-sm mt-2">
                                    @csrf
                                    <input type="text" name="review_notes" class="form-control" placeholder="Alasan penolakan..." required>
                                    <button class="btn btn-outline-danger">Tolak</button>
                                </form>
                            @else
                                <span class="text-muted small">
                                    @if ($request->review_notes) "{{ $request->review_notes }}" @else Selesai diproses @endif
                                </span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" class="text-center text-muted py-5">Belum ada permintaan akses.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">
        {{ $requests->links() }}
    </div>
</div>
@endsection

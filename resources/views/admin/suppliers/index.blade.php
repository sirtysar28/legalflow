@extends('layouts.app')
@section('title', 'Supplier')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Kelola Supplier</h4>
        <p class="text-muted mb-0">Data supplier &amp; status Supplier Assessment untuk pembuatan agreement</p>
    </div>
    <a href="{{ route('admin.suppliers.create') }}" class="btn btn-primary"><i class="bi bi-plus-circle me-1"></i>Tambah Supplier</a>
</div>

<div class="card lf-card">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>Supplier</th><th>Kontak</th><th>Check Supplier Assessment</th>
                    <th>Skor</th><th>Agreement</th><th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($suppliers as $supplier)
                    <tr>
                        <td>
                            <strong>{{ $supplier->name }}</strong>
                            <div class="small text-muted">NPWP: {{ $supplier->npwp ?? '-' }}</div>
                        </td>
                        <td class="small">
                            {{ $supplier->contact_person ?? '-' }}<br>
                            {{ $supplier->phone ?? '-' }} · {{ $supplier->email ?? '-' }}
                        </td>
                        <td class="small">
                            <span class="{{ $supplier->is_registered ? 'req-done' : 'req-missing' }}"><i class="bi bi-{{ $supplier->is_registered ? 'check' : 'x' }}-circle"></i> Terdaftar</span> ·
                            <span class="{{ $supplier->assessment_available ? 'req-done' : 'req-missing' }}"><i class="bi bi-{{ $supplier->assessment_available ? 'check' : 'x' }}-circle"></i> Assessment</span> ·
                            <span class="{{ $supplier->data_complete ? 'req-done' : 'req-missing' }}"><i class="bi bi-{{ $supplier->data_complete ? 'check' : 'x' }}-circle"></i> Data</span> ·
                            <span class="{{ $supplier->documents_complete ? 'req-done' : 'req-missing' }}"><i class="bi bi-{{ $supplier->documents_complete ? 'check' : 'x' }}-circle"></i> Dokumen</span>
                            <div class="mt-1">
                                @if ($supplier->assessmentPassed())
                                    <span class="badge bg-success">Lolos Assessment</span>
                                @else
                                    <span class="badge bg-warning text-dark">Belum Lolos</span>
                                @endif
                            </div>
                        </td>
                        <td>{{ $supplier->assessment_score ? $supplier->assessment_score . ' / 100' : '-' }}</td>
                        <td>{{ $supplier->applications_count }}</td>
                        <td class="text-end">
                            <a href="{{ route('admin.suppliers.edit', $supplier) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i> Edit</a>
                            <form method="POST" action="{{ route('admin.suppliers.destroy', $supplier) }}" class="d-inline"
                                  onsubmit="return confirm('Hapus supplier ini?')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted py-5">Belum ada supplier.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer bg-white py-3">{{ $suppliers->links() }}</div>
</div>
@endsection

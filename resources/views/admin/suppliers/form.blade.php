@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier' : 'Tambah Supplier')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <h4 class="fw-bold mb-0">{{ $supplier->exists ? 'Edit Supplier: ' . $supplier->name : 'Tambah Supplier Baru' }}</h4>
    <a href="{{ route('admin.suppliers.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i>Kembali</a>
</div>

<div class="card lf-card" style="max-width: 920px;">
    <div class="card-body">
        <form method="POST" action="{{ $supplier->exists ? route('admin.suppliers.update', $supplier) : route('admin.suppliers.store') }}">
            @csrf
            @if ($supplier->exists) @method('PUT') @endif

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Supplier <span class="text-danger">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $supplier->name) }}" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">NPWP</label>
                    <input type="text" name="npwp" value="{{ old('npwp', $supplier->npwp) }}" class="form-control">
                </div>
                <div class="col-12">
                    <label class="form-label">Alamat</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $supplier->address) }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Telepon</label>
                    <input type="text" name="phone" value="{{ old('phone', $supplier->phone) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email', $supplier->email) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Contact Person</label>
                    <input type="text" name="contact_person" value="{{ old('contact_person', $supplier->contact_person) }}" class="form-control">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="fw-semibold"><i class="bi bi-clipboard2-check me-2"></i>Check Supplier Assessment</h6>
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_registered" value="1" id="isRegistered"
                                   {{ old('is_registered', $supplier->is_registered ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="isRegistered">Terdaftar</label>
                            <div class="text-muted" style="font-size:.75rem">Supplier sudah terdaftar?</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="assessment_available" value="1" id="assessmentAvailable"
                                   {{ old('assessment_available', $supplier->assessment_available ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="assessmentAvailable">Assessment Tersedia</label>
                            <div class="text-muted" style="font-size:.75rem">Supplier assessment ada?</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="data_complete" value="1" id="dataComplete"
                                   {{ old('data_complete', $supplier->data_complete ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="dataComplete">Data Lengkap</label>
                            <div class="text-muted" style="font-size:.75rem">Data supplier lengkap?</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded-3 p-3 h-100">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="documents_complete" value="1" id="documentsComplete"
                                   {{ old('documents_complete', $supplier->documents_complete ?? false) ? 'checked' : '' }}>
                            <label class="form-check-label small fw-semibold" for="documentsComplete">Dokumen Lengkap</label>
                            <div class="text-muted" style="font-size:.75rem">Dokumen supplier lengkap?</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Skor Assessment (0-100)</label>
                    <input type="number" step="0.01" min="0" max="100" name="assessment_score"
                           value="{{ old('assessment_score', $supplier->assessment_score) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Tanggal Assessment</label>
                    <input type="date" name="assessment_date" value="{{ old('assessment_date', $supplier->assessment_date?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Risk Level</label>
                    <select name="risk_level" class="form-select">
                        <option value="">— Belum Dinilai —</option>
                        @foreach (['LOW' => 'Low Risk', 'MEDIUM' => 'Medium Risk', 'HIGH' => 'High Risk'] as $value => $label)
                            <option value="{{ $value }}" {{ old('risk_level', $supplier->risk_level) === $value ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label">Catatan</label>
                    <input type="text" name="notes" value="{{ old('notes', $supplier->notes) }}" class="form-control">
                </div>
            </div>

            <button class="btn btn-primary mt-4"><i class="bi bi-save me-1"></i>Simpan Supplier</button>
        </form>
    </div>
</div>
@endsection

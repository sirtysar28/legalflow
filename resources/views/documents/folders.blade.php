@extends('layouts.app')
@php($user = auth()->user())
@section('title', 'Kelola Folder Dokumen')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h4 class="fw-bold mb-1">Document Management — Kelola Folder</h4>
        <p class="text-muted mb-0">Folder manual &amp; sub-folder untuk penyimpanan terpusat dokumen</p>
    </div>
    <a href="{{ route('documents.browse') }}" class="btn btn-outline-secondary">
        <i class="bi bi-folder-check me-1"></i>Kembali ke Dokumen Terbit
    </a>
</div>

<div class="row g-3">
    <div class="col-lg-4">
        <div class="card lf-card">
            <div class="card-header bg-white py-3 fw-semibold"><i class="bi bi-plus-circle me-2"></i>Buat Folder</div>
            <div class="card-body">
                <form method="POST" action="{{ route('documents.folders.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Nama Folder <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="{{ old('name') }}" class="form-control @error('name') is-invalid @enderror"
                               placeholder="Contoh: Legal 2026" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Folder Induk (opsional — untuk sub-folder)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">— Tanpa induk (folder utama) —</option>
                            @foreach (\App\Models\DocumentFolder::orderBy('name')->get() as $folder)
                                <option value="{{ $folder->id }}">{{ $folder->fullPath(' / ') }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-folder-plus me-1"></i>Buat Folder</button>
                </form>
                <hr>
                <div class="alert alert-info small mb-0">
                    <i class="bi bi-info-circle me-1"></i>Folder otomatis <code>Document Management / {Divisi} / {Kategori} / {Nomor}</code> dibuat sistem saat pengajuan disetujui — tidak perlu dibuat manual.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card lf-card">
            <div class="card-header bg-white py-3 fw-semibold">
                <i class="bi bi-diagram-2 me-2"></i>Struktur Folder Manual
            </div>
            <div class="card-body">
                @if ($roots->isEmpty())
                    <p class="text-muted mb-0 text-center py-4">Belum ada folder manual. Buat folder pertama di form sebelah kiri.</p>
                @else
                    <div class="folder-tree">
                        @include('documents.partials.folder-tree', ['folders' => $roots, 'level' => 0])
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

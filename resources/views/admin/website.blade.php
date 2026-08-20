@extends('layouts.app')

@section('title', 'Kelola Website')

@php($hero = $content['hero'])

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--lf-navy)"><i class="bi bi-window-desktop me-2"></i>Kelola Website</h4>
            <div class="text-muted small">Atur seluruh teks &amp; gambar yang tampil di halaman depan (landing page).</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('landing') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-box-arrow-up-right me-1"></i>Lihat Halaman
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.website.update') }}" enctype="multipart/form-data" id="web-form">
        @csrf

        <div class="card lf-card">
            <div class="card-header bg-white py-3">
                <ul class="nav nav-pills gap-1 lf-settings-nav flex-nowrap" role="tablist">
                    <li class="nav-item"><button class="nav-link active" data-bs-toggle="pill" data-bs-target="#w-hero" type="button"><i class="bi bi-stars me-1"></i><span class="d-none d-sm-inline">Hero / Banner</span><span class="d-sm-none">Hero</span></button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#w-fitur" type="button"><i class="bi bi-grid me-1"></i><span class="d-none d-sm-inline">Fitur</span></button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#w-alur" type="button"><i class="bi bi-diagram-3 me-1"></i><span class="d-none d-sm-inline">Alur</span></button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#w-statistik" type="button"><i class="bi bi-graph-up me-1"></i><span class="d-none d-sm-inline">Statistik</span></button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#w-modul" type="button"><i class="bi bi-boxes me-1"></i><span class="d-none d-sm-inline">Modul</span></button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#w-cta" type="button"><i class="bi bi-megaphone me-1"></i><span class="d-none d-sm-inline">CTA &amp; Footer</span><span class="d-sm-none">CTA</span></button></li>
                    <li class="nav-item"><button class="nav-link" data-bs-toggle="pill" data-bs-target="#w-seo" type="button"><i class="bi bi-search me-1"></i><span class="d-none d-sm-inline">SEO</span></button></li>
                </ul>
            </div>

            <div class="card-body p-3 p-md-4">
                <div class="tab-content">

                    {{-- ================= HERO ================= --}}
                    <div class="tab-pane fade show active" id="w-hero" role="tabpanel">
                        @include('admin.website.partials.hero')
                    </div>

                    {{-- ================= FITUR ================= --}}
                    <div class="tab-pane fade" id="w-fitur" role="tabpanel">
                        @include('admin.website.partials.features', ['section' => 'features', 'listKey' => 'items', 'colors' => ['blue' => 'Biru', 'green' => 'Hijau', 'amber' => 'Kuning', 'purple' => 'Ungu'], 'hasPoints' => true])
                    </div>

                    {{-- ================= ALUR ================= --}}
                    <div class="tab-pane fade" id="w-alur" role="tabpanel">
                        @include('admin.website.partials.features', ['section' => 'workflow', 'listKey' => 'steps', 'colors' => ['blue' => 'Biru', 'green' => 'Hijau', 'amber' => 'Kuning'], 'hasPoints' => false])
                    </div>

                    {{-- ================= STATISTIK ================= --}}
                    <div class="tab-pane fade" id="w-statistik" role="tabpanel">
                        @include('admin.website.partials.stats')
                    </div>

                    {{-- ================= MODUL ================= --}}
                    <div class="tab-pane fade" id="w-modul" role="tabpanel">
                        @include('admin.website.partials.modules')
                    </div>

                    {{-- ================= CTA + FOOTER ================= --}}
                    <div class="tab-pane fade" id="w-cta" role="tabpanel">
                        @include('admin.website.partials.cta')
                    </div>

                    {{-- ================= SEO ================= --}}
                    <div class="tab-pane fade" id="w-seo" role="tabpanel">
                        @include('admin.website.partials.seo')
                    </div>

                </div>
            </div>

            <div class="card-footer bg-white py-3 d-flex flex-wrap gap-2 justify-content-between align-items-center">
                <span class="text-muted small"><i class="bi bi-info-circle me-1"></i>Perubahan langsung tampil di halaman depan setelah disimpan.</span>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check2-circle me-1"></i>Simpan Semua Perubahan
                </button>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script>
        // ---------- Daftar item dinamis (tambah / hapus baris) ----------
        document.querySelectorAll('[data-list-add]').forEach(function (btn) {
            btn.addEventListener('click', function () {
                const template = document.querySelector(this.dataset.listAdd);
                if (!template) return;
                const clone = template.content.cloneNode(true);
                const container = document.querySelector(this.dataset.listTarget);
                const index = container.querySelectorAll('.lf-item').length;
                clone.querySelectorAll('[name]').forEach(function (input) {
                    input.name = input.name.replace('__IDX__', index);
                });
                const wrapper = document.createElement('div');
                wrapper.appendChild(clone);
                container.appendChild(wrapper);
            });
        });

        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-item-remove]');
            if (!btn) return;
            const container = document.querySelector(btn.dataset.itemRemove);
            const items = container.querySelectorAll('.lf-item');
            if (items.length <= 1) { alert('Minimal 1 item harus tersisa.'); return; }
            btn.closest('.lf-item').remove();

            // Re-index nama input agar tetap urut.
            items !== null && container.querySelectorAll('.lf-item').forEach(function (item, i) {
                item.querySelectorAll('[name]').forEach(function (input) {
                    input.name = input.name.replace(/\[\d+\]/, '[' + i + ']');
                });
            });
        });
    </script>
@endpush

@php($stats = $content['stats'])

<div class="d-flex align-items-center justify-content-between mb-2">
    <span class="fw-semibold small" style="color:var(--lf-navy)"><i class="bi bi-graph-up me-1"></i>Angka Statistik ({{ count($stats['items']) }})</span>
    <button type="button" class="btn btn-sm btn-outline-primary" data-list-add="#tpl-stats" data-list-target="#list-stats">
        <i class="bi bi-plus-lg me-1"></i>Tambah
    </button>
</div>

<div id="list-stats" class="row g-3">
    @foreach ($stats['items'] as $i => $item)
        <div class="col-md-6 col-xl-3 lf-item">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge text-bg-light">Stat #{{ $i + 1 }}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-item-remove="#list-stats" tabindex="-1"><i class="bi bi-trash"></i></button>
                </div>
                <div class="row g-2">
                    <div class="col-6">
                        <label class="form-label small mb-1">Angka</label>
                        <input type="text" name="sections[stats][items][{{ $i }}][value]" class="form-control form-control-sm" value="{{ $item['value'] }}" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label small mb-1">Akhiran</label>
                        <input type="text" name="sections[stats][items][{{ $i }}][suffix]" class="form-control form-control-sm" value="{{ $item['suffix'] }}" placeholder="%, +, /7">
                    </div>
                    <div class="col-12">
                        <label class="form-label small mb-1">Label</label>
                        <input type="text" name="sections[stats][items][{{ $i }}][label]" class="form-control form-control-sm" value="{{ $item['label'] }}" required>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="form-text mt-2">Angka akan dianimasikan (count-up) saat halaman depan dibuka. Isi angka saja tanpa titik/koma, mis. <code>100</code>.</div>

<template id="tpl-stats">
    <div class="col-md-6 col-xl-3 lf-item">
        <div class="border rounded-3 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-light">Stat baru</span>
                <button type="button" class="btn btn-sm btn-outline-danger" data-item-remove="#list-stats" tabindex="-1"><i class="bi bi-trash"></i></button>
            </div>
            <div class="row g-2">
                <div class="col-6">
                    <label class="form-label small mb-1">Angka</label>
                    <input type="text" name="sections[stats][items][__IDX__][value]" class="form-control form-control-sm" value="" required>
                </div>
                <div class="col-6">
                    <label class="form-label small mb-1">Akhiran</label>
                    <input type="text" name="sections[stats][items][__IDX__][suffix]" class="form-control form-control-sm" value="" placeholder="%, +, /7">
                </div>
                <div class="col-12">
                    <label class="form-label small mb-1">Label</label>
                    <input type="text" name="sections[stats][items][__IDX__][label]" class="form-control form-control-sm" value="" required>
                </div>
            </div>
        </div>
    </div>
</template>

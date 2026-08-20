@php($modules = $content['modules'])
@php($colors = ['blue' => 'Biru', 'green' => 'Hijau', 'amber' => 'Kuning'])

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label fw-semibold small">Label Kecil (Eyebrow)</label>
        <input type="text" name="sections[modules][eyebrow]" class="form-control" value="{{ $modules['eyebrow'] }}" required>
    </div>
    <div class="col-md-8">
        <label class="form-label fw-semibold small">Judul Section</label>
        <input type="text" name="sections[modules][title]" class="form-control" value="{{ $modules['title'] }}" required>
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-2">
    <span class="fw-semibold small" style="color:var(--lf-navy)"><i class="bi bi-boxes me-1"></i>Kartu Modul ({{ count($modules['items']) }})</span>
    <button type="button" class="btn btn-sm btn-outline-primary" data-list-add="#tpl-modules" data-list-target="#list-modules">
        <i class="bi bi-plus-lg me-1"></i>Tambah
    </button>
</div>

<div id="list-modules" class="row g-3">
    @foreach ($modules['items'] as $i => $item)
        <div class="col-lg-4 lf-item">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge text-bg-light">Modul #{{ $i + 1 }}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-item-remove="#list-modules" tabindex="-1"><i class="bi bi-trash"></i></button>
                </div>
                <div class="row g-2">
                    <div class="col-8">
                        <label class="form-label small mb-1">Ikon Bootstrap</label>
                        <input type="text" name="sections[modules][items][{{ $i }}][icon]" class="form-control form-control-sm" value="{{ $item['icon'] }}" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small mb-1">Warna</label>
                        <select name="sections[modules][items][{{ $i }}][color]" class="form-select form-select-sm" required>
                            @foreach ($colors as $value => $label)
                                <option value="{{ $value }}" {{ $item['color'] === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small mb-1">Judul</label>
                        <input type="text" name="sections[modules][items][{{ $i }}][title]" class="form-control form-control-sm" value="{{ $item['title'] }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label small mb-1">Deskripsi</label>
                        <textarea name="sections[modules][items][{{ $i }}][desc]" rows="3" class="form-control form-control-sm" required>{{ $item['desc'] }}</textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small mb-1">Tag <span class="text-muted">(pisahkan koma)</span></label>
                        <input type="text" name="sections[modules][items][{{ $i }}][tags]" class="form-control form-control-sm" value="{{ $item['tags'] }}" placeholder="NIB, PBG, SLF">
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

<template id="tpl-modules">
    <div class="col-lg-4 lf-item">
        <div class="border rounded-3 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-light">Modul baru</span>
                <button type="button" class="btn btn-sm btn-outline-danger" data-item-remove="#list-modules" tabindex="-1"><i class="bi bi-trash"></i></button>
            </div>
            <div class="row g-2">
                <div class="col-8">
                    <label class="form-label small mb-1">Ikon Bootstrap</label>
                    <input type="text" name="sections[modules][items][__IDX__][icon]" class="form-control form-control-sm" value="bi bi-box" required>
                </div>
                <div class="col-4">
                    <label class="form-label small mb-1">Warna</label>
                    <select name="sections[modules][items][__IDX__][color]" class="form-select form-select-sm" required>
                        @foreach ($colors as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small mb-1">Judul</label>
                    <input type="text" name="sections[modules][items][__IDX__][title]" class="form-control form-control-sm" value="" required>
                </div>
                <div class="col-12">
                    <label class="form-label small mb-1">Deskripsi</label>
                    <textarea name="sections[modules][items][__IDX__][desc]" rows="3" class="form-control form-control-sm" required></textarea>
                </div>
                <div class="col-12">
                    <label class="form-label small mb-1">Tag <span class="text-muted">(pisahkan koma)</span></label>
                    <input type="text" name="sections[modules][items][__IDX__][tags]" class="form-control form-control-sm" value="" placeholder="NIB, PBG, SLF">
                </div>
            </div>
        </div>
    </div>
</template>

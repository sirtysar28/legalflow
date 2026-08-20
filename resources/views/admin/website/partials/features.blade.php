@php($sectionData = $content[$section])
@php($items = $sectionData[$listKey])

<div class="mb-4">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label fw-semibold small">Label Kecil (Eyebrow)</label>
            <input type="text" name="sections[{{ $section }}][eyebrow]" class="form-control" value="{{ $sectionData['eyebrow'] }}" required>
        </div>
        <div class="col-md-8">
            <label class="form-label fw-semibold small">Judul Section</label>
            <input type="text" name="sections[{{ $section }}][title]" class="form-control" value="{{ $sectionData['title'] }}" required>
        </div>
        @if ($section === 'features')
            <div class="col-12">
                <label class="form-label fw-semibold small">Subjudul</label>
                <input type="text" name="sections[{{ $section }}][subtitle]" class="form-control" value="{{ $sectionData['subtitle'] }}" required>
            </div>
        @endif
    </div>
</div>

<div class="d-flex align-items-center justify-content-between mb-2">
    <span class="fw-semibold small" style="color:var(--lf-navy)">
        <i class="bi bi-list-ul me-1"></i>{{ $listKey === 'steps' ? 'Langkah Alur' : 'Kartu Item' }} ({{ count($items) }})
    </span>
    <button type="button" class="btn btn-sm btn-outline-primary" data-list-add="#tpl-{{ $section }}" data-list-target="#list-{{ $section }}">
        <i class="bi bi-plus-lg me-1"></i>Tambah
    </button>
</div>

<div id="list-{{ $section }}" class="row g-3">
    @foreach ($items as $i => $item)
        <div class="col-md-6 lf-item">
            <div class="border rounded-3 p-3 h-100">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="badge text-bg-light">Item #{{ $i + 1 }}</span>
                    <button type="button" class="btn btn-sm btn-outline-danger" data-item-remove="#list-{{ $section }}" tabindex="-1">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-8">
                        <label class="form-label small mb-1">Ikon Bootstrap</label>
                        <input type="text" name="sections[{{ $section }}][{{ $listKey }}][{{ $i }}][icon]" class="form-control form-control-sm" value="{{ $item['icon'] }}" placeholder="bi bi-file-earmark-text" required>
                    </div>
                    <div class="col-4">
                        <label class="form-label small mb-1">Warna</label>
                        <select name="sections[{{ $section }}][{{ $listKey }}][{{ $i }}][{{ $listKey === 'steps' ? 'accent' : 'color' }}]" class="form-select form-select-sm" required>
                            @foreach ($colors as $value => $label)
                                <option value="{{ $value }}" {{ ($listKey === 'steps' ? $item['accent'] : $item['color']) === $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label small mb-1">Judul</label>
                        <input type="text" name="sections[{{ $section }}][{{ $listKey }}][{{ $i }}][title]" class="form-control form-control-sm" value="{{ $item['title'] }}" required>
                    </div>
                    @if ($hasPoints)
                        <div class="col-12">
                            <label class="form-label small mb-1">Poin-poin <span class="text-muted">(satu per baris)</span></label>
                            <textarea name="sections[{{ $section }}][{{ $listKey }}][{{ $i }}][points]" rows="4" class="form-control form-control-sm" required>{{ $item['points'] }}</textarea>
                        </div>
                    @else
                        <div class="col-12">
                            <label class="form-label small mb-1">Deskripsi singkat</label>
                            <input type="text" name="sections[{{ $section }}][{{ $listKey }}][{{ $i }}][desc]" class="form-control form-control-sm" value="{{ $item['desc'] }}" required>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>

<template id="tpl-{{ $section }}">
    <div class="col-md-6 lf-item">
        <div class="border rounded-3 p-3 h-100">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="badge text-bg-light">Item baru</span>
                <button type="button" class="btn btn-sm btn-outline-danger" data-item-remove="#list-{{ $section }}" tabindex="-1">
                    <i class="bi bi-trash"></i>
                </button>
            </div>
            <div class="row g-2">
                <div class="col-8">
                    <label class="form-label small mb-1">Ikon Bootstrap</label>
                    <input type="text" name="sections[{{ $section }}][{{ $listKey }}][__IDX__][icon]" class="form-control form-control-sm" value="bi bi-star" placeholder="bi bi-file-earmark-text" required>
                </div>
                <div class="col-4">
                    <label class="form-label small mb-1">Warna</label>
                    <select name="sections[{{ $section }}][{{ $listKey }}][__IDX__][{{ $listKey === 'steps' ? 'accent' : 'color' }}]" class="form-select form-select-sm" required>
                        @foreach ($colors as $value => $label)
                            <option value="{{ $value }}">{{ $label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small mb-1">Judul</label>
                    <input type="text" name="sections[{{ $section }}][{{ $listKey }}][__IDX__][title]" class="form-control form-control-sm" value="" required>
                </div>
                @if ($hasPoints)
                    <div class="col-12">
                        <label class="form-label small mb-1">Poin-poin <span class="text-muted">(satu per baris)</span></label>
                        <textarea name="sections[{{ $section }}][{{ $listKey }}][__IDX__][points]" rows="4" class="form-control form-control-sm" required></textarea>
                    </div>
                @else
                    <div class="col-12">
                        <label class="form-label small mb-1">Deskripsi singkat</label>
                        <input type="text" name="sections[{{ $section }}][{{ $listKey }}][__IDX__][desc]" class="form-control form-control-sm" value="" required>
                    </div>
                @endif
            </div>
        </div>
    </div>
</template>

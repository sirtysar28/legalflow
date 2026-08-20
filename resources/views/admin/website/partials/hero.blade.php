@php($hero = $content['hero'])

<div class="row g-4">
    <div class="col-xl-8">
        <div class="border rounded-3 p-3 p-md-4 h-100">
            <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-textarea-t me-2"></i>Teks Hero / Banner</h6>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Badge Kecil (atas judul)</label>
                    <input type="text" name="sections[hero][badge]" class="form-control" value="{{ $hero['badge'] }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Teks Scroll (bawah hero)</label>
                    <input type="text" name="sections[hero][scroll_hint]" class="form-control" value="{{ $hero['scroll_hint'] }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Judul — Kata Awal</label>
                    <input type="text" name="sections[hero][title_start]" class="form-control" value="{{ $hero['title_start'] }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold small">Judul — Baris Kedua</label>
                    <input type="text" name="sections[hero][title_end]" class="form-control" value="{{ $hero['title_end'] }}" required>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Kata Efek Mengetik <span class="text-muted fw-normal">(pisahkan dengan koma)</span></label>
                    <input type="text" name="sections[hero][typing_words]" class="form-control" value="{{ $hero['typing_words'] }}" required>
                    <div class="form-text">Contoh: <code>Perizinan, Agreement, Dokumen Legal</code></div>
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Paragraf Pengantar</label>
                    <textarea name="sections[hero][lead]" rows="4" class="form-control" required>{{ $hero['lead'] }}</textarea>
                    <div class="form-text">Boleh pakai <code>**teks tebal**</code> untuk penekanan.</div>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Tombol Utama</label>
                    <input type="text" name="sections[hero][cta_primary]" class="form-control" value="{{ $hero['cta_primary'] }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Tombol Sekunder</label>
                    <input type="text" name="sections[hero][cta_secondary]" class="form-control" value="{{ $hero['cta_secondary'] }}" required>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex align-items-center justify-content-between mb-2">
                <label class="form-label fw-semibold small mb-0">Poin Keunggulan <span class="text-muted fw-normal">(maks. 3–4, tampil di bawah tombol)</span></label>
                <button type="button" class="btn btn-sm btn-outline-primary" data-list-add="#tpl-highlight" data-list-target="#highlight-list">
                    <i class="bi bi-plus-lg"></i>
                </button>
            </div>
            <div id="highlight-list">
                @foreach (array_pad($hero['highlights'], max(count($hero['highlights']), 1), '') as $i => $line)
                    <div class="input-group mb-2 lf-item">
                        <span class="input-group-text bg-white"><i class="bi bi-check-circle-fill text-success"></i></span>
                        <input type="text" name="sections[hero][highlights][{{ $i }}]" class="form-control" value="{{ $line }}" placeholder="Contoh: Review berjenjang Legal & Admin" maxlength="100">
                        <button type="button" class="btn btn-outline-danger" data-item-remove="#highlight-list" tabindex="-1"><i class="bi bi-x-lg"></i></button>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="border rounded-3 p-3 p-md-4 mb-4">
            <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-image me-2"></i>Gambar Hero <span class="text-muted fw-normal small">(opsional)</span></h6>
            <div class="text-center mb-3">
                @if ($heroImage)
                    <img src="{{ $heroImage }}" alt="Gambar hero" class="img-fluid rounded-3 border shadow-sm" style="max-height:180px;">
                @else
                    <div class="border rounded-3 bg-light-subtle d-inline-flex align-items-center justify-content-center text-muted" style="width:100%;height:130px;">
                        <div class="small"><i class="bi bi-card-image d-block mb-1" style="font-size:1.5rem"></i>Belum ada gambar<br><span class="text-secondary" style="font-size:.72rem">(tampil kartu animasi)</span></div>
                    </div>
                @endif
            </div>
            <input type="file" name="hero_image" accept=".jpg,.jpeg,.png,.webp" class="form-control form-control-sm">
            <div class="form-text">Disarankan rasio 4:3 / 1:1, minimal 800px, maks 3 MB. Jika diisi, gambar menggantikan kartu animasi di kanan hero.</div>
            @if ($heroImage)
                <div class="form-check mt-2">
                    <input class="form-check-input" type="checkbox" name="remove_hero_image" value="1" id="rmHeroImg">
                    <label class="form-check-label small text-danger" for="rmHeroImg">Hapus gambar (kembali ke kartu animasi)</label>
                </div>
            @endif
        </div>

        <div class="border rounded-3 p-3 p-md-4 bg-light-subtle">
            <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
            <ul class="small text-muted mb-0 ps-3">
                <li class="mb-2">Judul kata awal + kata efek mengetik digabung, contoh: <em>"Kelola Perizinan"</em>.</li>
                <li class="mb-2">Paragraf singkat &amp; jelas lebih efektif.</li>
            </ul>
        </div>
    </div>
</div>

<template id="tpl-highlight">
    <div class="input-group mb-2 lf-item">
        <span class="input-group-text bg-white"><i class="bi bi-check-circle-fill text-success"></i></span>
        <input type="text" name="sections[hero][highlights][__IDX__]" class="form-control" value="" placeholder="Poin keunggulan baru" maxlength="100">
        <button type="button" class="btn btn-outline-danger" data-item-remove="#highlight-list" tabindex="-1"><i class="bi bi-x-lg"></i></button>
    </div>
</template>

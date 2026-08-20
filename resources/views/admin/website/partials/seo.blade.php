@php($seo = $content['seo'])

<div class="row g-4">
    <div class="col-lg-8">
        <div class="border rounded-3 p-3 p-md-4 h-100">
            <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-search me-2"></i>SEO Halaman Depan</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Title Tag (judul di tab browser &amp; hasil pencarian)</label>
                <input type="text" name="sections[seo][title]" class="form-control" value="{{ $seo['title'] }}" required>
            </div>
            <div>
                <label class="form-label fw-semibold small">Meta Description</label>
                <textarea name="sections[seo][description]" rows="3" class="form-control" required>{{ $seo['description'] }}</textarea>
                <div class="form-text">Maks. ±160 karakter disarankan agar tidak terpotong di Google.</div>
            </div>
        </div>
    </div>
</div>

@php($cta = $content['cta'])
@php($footer = $content['footer'])

<div class="row g-4">
    <div class="col-lg-6">
        <div class="border rounded-3 p-3 p-md-4 h-100">
            <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-megaphone me-2"></i>Banner CTA (bawah halaman)</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Judul</label>
                <input type="text" name="sections[cta][title]" class="form-control" value="{{ $cta['title'] }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Teks</label>
                <textarea name="sections[cta][text]" rows="3" class="form-control" required>{{ $cta['text'] }}</textarea>
            </div>
            <div>
                <label class="form-label fw-semibold small">Teks Tombol</label>
                <input type="text" name="sections[cta][button]" class="form-control" value="{{ $cta['button'] }}" required>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="border rounded-3 p-3 p-md-4 h-100">
            <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-footer me-2"></i>Footer</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold small">Tagline</label>
                <input type="text" name="sections[footer][tagline]" class="form-control" value="{{ $footer['tagline'] }}" required>
            </div>
            <div>
                <label class="form-label fw-semibold small">Hak Cipta</label>
                <input type="text" name="sections[footer][copyright]" class="form-control" value="{{ $footer['copyright'] }}" required>
                <div class="form-text">Gunakan <code>:year</code> untuk tahun berjalan otomatis, mis. <code>© :year PT Trijaya Solution</code>.</div>
            </div>
        </div>
    </div>
</div>

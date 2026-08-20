<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --lf-navy: #0f1e3d;
            --lf-navy-2: #16294f;
            --lf-accent: #2d5da8;
            --lf-accent-2: #4a7fd4;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; min-height: 100vh; display: flex;
            font-family: 'Segoe UI', system-ui, -apple-system, 'Helvetica Neue', Arial, sans-serif;
            background: #0f1e3d;
        }

        /* ================= PANEL KIRI — BRANDING ================= */
        .login-visual {
            flex: 0 0 46%; max-width: 46%; position: relative; overflow: hidden;
            background: linear-gradient(150deg, #0f1e3d 0%, #16294f 45%, #2d5da8 115%);
            color: #fff; display: flex; flex-direction: column;
            justify-content: space-between; padding: 2.6rem 2.9rem;
        }
        .login-visual::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,.14) 1px, transparent 1px);
            background-size: 26px 26px; opacity: .35; pointer-events: none;
        }
        .blob { position: absolute; border-radius: 50%; filter: blur(70px); opacity: .4; pointer-events: none; animation: floatBlob 16s ease-in-out infinite alternate; }
        .blob-1 { width: 380px; height: 380px; background: #4a7fd4; top: -130px; right: -110px; }
        .blob-2 { width: 320px; height: 320px; background: #6a4c93; bottom: -110px; left: -90px; animation-delay: -8s; }
        @keyframes floatBlob {
            from { transform: translate(0,0) scale(1); }
            to   { transform: translate(-36px, 28px) scale(1.18); }
        }
        .visual-inner { position: relative; z-index: 1; }

        .visual-brand { display: flex; align-items: center; gap: .9rem; }
        .visual-brand img {
            width: 52px; height: 52px; object-fit: cover; border-radius: .9rem;
            background: #fff; box-shadow: 0 6px 18px rgba(0,0,0,.35);
        }
        .visual-brand-name { font-size: 1.25rem; font-weight: 800; letter-spacing: .4px; }
        .visual-brand-sub { font-size: .74rem; color: rgba(255,255,255,.65); }

        .visual-hero { max-width: 420px; }
        .visual-hero h1 {
            font-size: clamp(1.55rem, 2.4vw, 2.1rem); font-weight: 800; line-height: 1.25; margin: 0 0 .75rem;
        }
        .visual-hero p { color: rgba(255,255,255,.78); font-size: .95rem; line-height: 1.7; margin-bottom: 1.6rem; }
        .visual-feature { display: flex; align-items: flex-start; gap: .8rem; margin-bottom: 1rem; }
        .visual-feature .vf-icon {
            width: 38px; height: 38px; flex-shrink: 0; border-radius: .75rem;
            background: rgba(255,255,255,.14); border: 1px solid rgba(255,255,255,.18);
            display: inline-flex; align-items: center; justify-content: center; font-size: 1.05rem;
        }
        .visual-feature .vf-title { font-weight: 700; font-size: .92rem; }
        .visual-feature .vf-desc { font-size: .78rem; color: rgba(255,255,255,.68); }

        .visual-footer { font-size: .74rem; color: rgba(255,255,255,.55); position: relative; z-index: 1; }

        /* ================= PANEL KANAN — FORM ================= */
        .login-panel {
            flex: 1; display: flex; align-items: center; justify-content: center;
            padding: 2rem 1.25rem; background: #f4f7fc; position: relative;
        }
        .login-box { width: 100%; max-width: 430px; animation: riseIn .55s ease both; }
        @keyframes riseIn {
            from { opacity: 0; transform: translateY(18px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        .login-logo-mobile { display: none; }
        .login-card {
            background: #fff; border-radius: 1.4rem; padding: 2.35rem 2.2rem 2rem;
            box-shadow: 0 22px 60px rgba(10, 18, 38, .13);
            border: 1px solid rgba(230, 234, 242, .8);
        }
        .login-title { font-size: 1.5rem; font-weight: 800; color: var(--lf-navy); margin: 0 0 .3rem; }
        .login-sub { color: #64748b; font-size: .88rem; margin-bottom: 1.75rem; }

        /* Input modern: ikon di kiri, tinggi nyaman, fokus bercahaya */
        .lf-field { position: relative; margin-bottom: 1.05rem; }
        .lf-field > .lf-field-icon {
            position: absolute; left: 1rem; top: 50%; transform: translateY(-50%);
            color: #94a3b8; font-size: 1rem; pointer-events: none; transition: color .2s;
        }
        .lf-field .form-control {
            height: 48px; padding: .6rem 2.9rem .6rem 2.85rem; border-radius: .8rem;
            border: 1.6px solid #dde3ee; background: #f9fafd; font-size: .92rem;
            transition: border-color .2s, box-shadow .2s, background .2s;
        }
        .lf-field .form-control:focus {
            background: #fff; border-color: var(--lf-accent-2);
            box-shadow: 0 0 0 .25rem rgba(74,127,212,.16);
        }
        .lf-field:focus-within > .lf-field-icon { color: var(--lf-accent); }
        .lf-field .toggle-password {
            position: absolute; right: .45rem; top: 50%; transform: translateY(-50%);
            border: none; background: transparent; color: #94a3b8; padding: .45rem .6rem;
            border-radius: .6rem; line-height: 1;
        }
        .lf-field .toggle-password:hover { color: var(--lf-accent); background: #eef2f9; }

        .btn-submit {
            width: 100%; height: 48px; border: none; border-radius: .8rem;
            font-weight: 700; font-size: .95rem; color: #fff; letter-spacing: .2px;
            background: linear-gradient(135deg, #16294f, #2d5da8 70%, #3a6fc2);
            box-shadow: 0 10px 22px rgba(45, 93, 168, .38);
            transition: transform .12s ease, filter .2s ease, box-shadow .2s ease;
            display: inline-flex; align-items: center; justify-content: center; gap: .5rem;
        }
        .btn-submit:hover { filter: brightness(1.12); transform: translateY(-1px); box-shadow: 0 12px 26px rgba(45,93,168,.48); color:#fff; }
        .btn-submit:disabled { opacity: .85; }

        .login-meta { display: flex; align-items: center; justify-content: space-between; margin: .35rem 0 1.4rem; }
        .login-meta .form-check-label { font-size: .82rem; color: #52617f; }
        .login-meta .form-check-input { border-color: #cdd6e4; }
        .login-meta .form-check-input:checked { background-color: var(--lf-accent); border-color: var(--lf-accent); }

        /* Chips akun demo */
        .demo-wrap { margin-top: 1.4rem; text-align: center; }
        .demo-title {
            display: flex; align-items: center; gap: .8rem; color: #8a94a6;
            font-size: .74rem; text-transform: uppercase; letter-spacing: .12em; margin-bottom: .7rem;
        }
        .demo-title::before, .demo-title::after { content: ''; flex: 1; height: 1px; background: #e2e8f2; }
        .demo-chips { display: flex; flex-wrap: wrap; justify-content: center; gap: .45rem; }
        .demo-chip {
            border: 1px solid #dbe3f0; background: #f8fafd; color: #40507a;
            font-size: .76rem; border-radius: 99px; padding: .34rem .85rem; cursor: pointer;
            transition: all .18s ease; font-weight: 600;
        }
        .demo-chip:hover { border-color: var(--lf-accent-2); color: var(--lf-accent); background: #eef4fd; transform: translateY(-1px); }

        .login-footer { text-align: center; color: #8a94a6; font-size: .76rem; margin-top: 1.4rem; }

        /* ============ OVERLAY LOADER LOGIN ============ */
        #loginLoader {
            position: fixed; inset: 0; z-index: 2000; display: none;
            align-items: center; justify-content: center; flex-direction: column; gap: 1.1rem;
            background: rgba(13, 24, 48, .86); backdrop-filter: blur(7px); color: #fff;
        }
        #loginLoader.show { display: flex; }
        .loader-spinner {
            width: 54px; height: 54px; border-radius: 50%;
            border: 5px solid rgba(255,255,255,.18); border-top-color: #7fa8e8;
            animation: spin .8s linear infinite;
        }
        .loader-dots { display: flex; gap: 6px; }
        .loader-dots span {
            width: 8px; height: 8px; border-radius: 50%; background: #7fa8e8;
            animation: bounceDot 1.2s ease-in-out infinite;
        }
        .loader-dots span:nth-child(2) { animation-delay: .15s; }
        .loader-dots span:nth-child(3) { animation-delay: .3s; }
        @keyframes spin { to { transform: rotate(360deg); } }
        @keyframes bounceDot {
            0%, 80%, 100% { transform: scale(.6); opacity: .5; }
            40% { transform: scale(1); opacity: 1; }
        }
        #loginLoaderText { font-weight: 600; font-size: .95rem; letter-spacing: .3px; }
        #loginLoaderSub { font-size: .78rem; color: rgba(255,255,255,.6); margin-top: -.6rem; }

        /* ============ RESPONSIVE ============ */
        @media (max-width: 991.98px) {
            .login-visual { display: none; }
            .login-panel { padding: 1.5rem 1rem; background: linear-gradient(160deg,#f4f7fc, #e8eef9); }
            .login-logo-mobile { display: flex; flex-direction: column; align-items: center; gap: .8rem; margin-bottom: 1.5rem; animation: riseIn .5s ease both; }
            .login-logo-mobile img { width: 74px; height: 74px; border-radius: 1.1rem; object-fit: cover; background: #fff; box-shadow: 0 10px 26px rgba(20,33,61,.18); }
            .login-logo-mobile .m-name { font-weight: 800; color: var(--lf-navy); font-size: 1.1rem; }
        }
        @media (max-width: 480px) {
            .login-card { padding: 1.8rem 1.4rem 1.6rem; }
        }
    </style>
</head>
<body>

{{-- ================= PANEL KIRI: BRANDING ================= --}}
<aside class="login-visual">
    <div class="blob blob-1"></div>
    <div class="blob blob-2"></div>

    <div class="visual-inner visual-brand">
        <img src="{{ company_logo_url() }}" alt="Logo {{ company_name() }}">
        <div>
            <div class="visual-brand-name">{{ company_name() }}</div>
            <div class="visual-brand-sub">Legal Management Suite</div>
        </div>
    </div>

    <div class="visual-inner visual-hero">
        <h1>Satu Platform untuk<br>Seluruh Kebutuhan Legal</h1>
        <p>Ajukan perizinan &amp; agreement, pantau proses review, dan kelola dokumen legal perusahaan Anda secara terpusat dan aman.</p>

        <div class="visual-feature">
            <span class="vf-icon"><i class="bi bi-file-earmark-text"></i></span>
            <div>
                <div class="vf-title">Pengajuan Perizinan &amp; Agreement</div>
                <div class="vf-desc">NIB, PBG, SLF, UKL-UPL, Halal, TDG hingga kontrak lintas divisi</div>
            </div>
        </div>
        <div class="visual-feature">
            <span class="vf-icon"><i class="bi bi-clipboard-check"></i></span>
            <div>
                <div class="vf-title">Review &amp; Approval Terstruktur</div>
                <div class="vf-desc">Alur persetujuan, revisi, dan audit trail yang transparan</div>
            </div>
        </div>
        <div class="visual-feature">
            <span class="vf-icon"><i class="bi bi-folder-lock"></i></span>
            <div>
                <div class="vf-title">Document Management Aman</div>
                <div class="vf-desc">Penyimpanan terpusat dengan kontrol akses antar divisi</div>
            </div>
        </div>
    </div>

    <div class="visual-footer">
        &copy; {{ date('Y') }} {{ company_name() }} — PT Trijaya Solution
    </div>
</aside>

{{-- ================= PANEL KANAN: FORM LOGIN ================= --}}
<main class="login-panel">
    <div class="login-box">
        <div class="login-logo-mobile">
            <img src="{{ company_logo_url() }}" alt="Logo {{ company_name() }}">
            <div class="m-name">{{ company_name() }}</div>
        </div>

        <div class="login-card">
            <h1 class="login-title">Selamat Datang 👋</h1>
            <p class="login-sub">Masuk untuk mengelola pengajuan &amp; dokumen legal Anda.</p>

            @if (session('success'))
                <div class="alert alert-success py-2 px-3 small" style="border-radius:.7rem;">
                    <i class="bi bi-check-circle me-1"></i>{{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login.attempt') }}" id="loginForm" novalidate>
                @csrf

                <div class="lf-field">
                    <i class="bi bi-envelope lf-field-icon"></i>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email"
                           name="email" value="{{ old('email') }}" placeholder="nama@perusahaan.com"
                           required autofocus autocomplete="username">
                    @error('email')
                        <div class="invalid-feedback d-block small ps-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="lf-field">
                    <i class="bi bi-lock lf-field-icon"></i>
                    <input type="password" class="form-control @error('email') is-invalid @enderror" id="password"
                           name="password" placeholder="Password" required autocomplete="current-password">
                    <button class="toggle-password" type="button" data-target="#password" tabindex="-1"
                            aria-label="Tampilkan / sembunyikan password">
                        <i class="bi bi-eye"></i>
                    </button>
                    @error('email')
                        <div class="invalid-feedback d-block small ps-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="login-meta">
                    <div class="form-check mb-0">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label" for="remember">Ingat saya</label>
                    </div>
                    <span class="small text-muted"><i class="bi bi-shield-lock me-1"></i>Koneksi aman</span>
                </div>

                <button type="submit" class="btn-submit" id="loginBtn">
                    <i class="bi bi-box-arrow-in-right"></i> Masuk
                </button>
            </form>

            <div class="demo-wrap">
                <div class="demo-title">Akun Demo — klik untuk isi otomatis</div>
                <div class="demo-chips">
                    <button type="button" class="demo-chip" data-email="admin@legalflow.test">👑 Admin</button>
                    <button type="button" class="demo-chip" data-email="legal@legalflow.test">⚖️ Legal</button>
                    <button type="button" class="demo-chip" data-email="user@legalflow.test">👤 User</button>
                    <button type="button" class="demo-chip" data-email="budi@legalflow.test">🛒 Purchasing</button>
                </div>
            </div>
        </div>

        <div class="login-footer">
            Butuh bantuan? Hubungi tim IT / Legal internal perusahaan.
        </div>
    </div>
</main>

{{-- ================= OVERLAY LOADER ================= --}}
<div id="loginLoader" aria-live="polite">
    <div class="loader-spinner"></div>
    <div>
        <div id="loginLoaderText">Memverifikasi kredensial…</div>
        <div id="loginLoaderSub" class="text-center">Sedang menghubungkan ke server</div>
    </div>
    <div class="loader-dots"><span></span><span></span><span></span></div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ---------- Toggle ngintip password ----------
    document.querySelectorAll('.toggle-password').forEach(function (button) {
        button.addEventListener('click', function () {
            const input = document.querySelector(this.dataset.target);
            if (!input) return;
            const icon = this.querySelector('i');
            const show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            icon.classList.toggle('bi-eye', !show);
            icon.classList.toggle('bi-eye-slash', show);
            this.setAttribute('aria-label', show ? 'Sembunyikan password' : 'Tampilkan password');
        });
    });

    // ---------- Chip akun demo: isi otomatis ----------
    document.querySelectorAll('.demo-chip').forEach(function (chip) {
        chip.addEventListener('click', function () {
            const email = document.getElementById('email');
            const password = document.getElementById('password');
            email.value = this.dataset.email;
            password.value = 'password';
            password.focus();
        });
    });

    // ---------- Spinner saat proses login ----------
    const loginForm = document.getElementById('loginForm');
    const loginLoader = document.getElementById('loginLoader');
    const loaderText = document.getElementById('loginLoaderText');
    const loaderSub = document.getElementById('loginLoaderSub');
    const loginBtn = document.getElementById('loginBtn');

    loginForm.addEventListener('submit', function (e) {
        // Validasi native dulu — jangan tampilkan loader bila form kosong.
        if (!loginForm.checkValidity()) {
            e.preventDefault();
            loginForm.reportValidity();
            return;
        }

        loginBtn.disabled = true;
        loginBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status"></span> Memproses…';
        loaderText.textContent = 'Memverifikasi kredensial…';
        loaderSub.textContent = 'Sedang menghubungkan ke server';
        loginLoader.classList.add('show');

        // Eskalasi pesan bila proses memakan waktu (server SMTP / session).
        setTimeout(function () {
            loaderText.textContent = 'Menyiapkan dashboard Anda…';
            loaderSub.textContent = 'Mohon tunggu sebentar';
        }, 1300);
    });

    // Bila kembali via tombol back browser (BFCache), pastikan loader tersembunyi.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            loginLoader.classList.remove('show');
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="bi bi-box-arrow-in-right"></i> Masuk';
        }
    });
</script>
</body>
</html>

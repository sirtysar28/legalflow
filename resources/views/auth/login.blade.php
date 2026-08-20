<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Masuk — {{ config('app.name') }}</title>

    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">

    {{-- Google Font --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;500;600;700&display=swap" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --lf-blue: #2563eb;
            --lf-blue-dark: #2457d6;

            /* Tinggi elemen form menyesuaikan tinggi layar
               agar pas tanpa scroll di laptop resolusi kecil. */
            --lf-input-h: clamp(42px, 6vh, 52px);
        }

        html,
        body {
            height: 100%;
        }

        body {
            font-family: 'Sora', sans-serif;

            /* Halaman dikunci — tidak bisa di-scroll sama sekali.
               Scroll pengaman (bila perlu) terjadi di dalam panel. */
            overflow: hidden;

            background: #f7f9fc;
            color: #1f2937;
        }

        .login-wrapper {
            height: 100vh;
            height: 100dvh;
            display: flex;
            overflow: hidden;
        }

        /* =========================================
           LEFT PANEL
        ========================================= */

        .login-left {
            width: 50%;
            height: 100%;
            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;

            background: linear-gradient(
                135deg,
                #2563eb 0%,
                #285fe0 45%,
                #315ed9 100%
            );

            color: #ffffff;
        }

        /* Decorative circles */

        .circle {
            position: absolute;
            border-radius: 50%;
            pointer-events: none;
        }

        .circle-1 {
            width: 330px;
            height: 330px;
            top: -140px;
            right: -80px;
            background: rgba(255, 255, 255, 0.06);
        }

        .circle-2 {
            width: 260px;
            height: 260px;
            bottom: -130px;
            left: -100px;
            background: rgba(255, 255, 255, 0.055);
        }

        .circle-3 {
            width: 180px;
            height: 180px;
            top: 80px;
            left: -90px;
            background: rgba(255, 255, 255, 0.035);
        }

        .left-content {
            position: relative;
            z-index: 5;
            width: 100%;
            max-width: 650px;
            padding: clamp(26px, 6.5vh, 70px) 12%;
        }

        /* Logo */

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: clamp(20px, 4.5vh, 48px);
        }

        .brand-logo {
            width: clamp(40px, 5vh, 48px);
            height: clamp(40px, 5vh, 48px);

            display: flex;
            align-items: center;
            justify-content: center;

            border: 2px solid rgba(255,255,255,.9);
            border-radius: 50%;

            overflow: hidden;
            background: rgba(255,255,255,.12);
        }

        .brand-logo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 50%;
        }

        .brand-name {
            font-size: 20px;
            line-height: 1.15;
            font-weight: 700;
            letter-spacing: .3px;
            text-transform: uppercase;
        }

        /* Heading */

        .hero-title {
            font-size: clamp(30px, 1.8vw + 3.2vh, 54px);
            line-height: 1.12;
            font-weight: 700;
            letter-spacing: -1.5px;

            margin-bottom: clamp(12px, 3vh, 24px);
        }

        .hero-description {
            max-width: 560px;

            font-size: clamp(12.5px, 1.9vh, 16px);
            line-height: 1.7;

            color: rgba(255,255,255,.86);

            margin-bottom: clamp(18px, 5vh, 48px);
        }

        /* Feature */

        .feature {
            display: flex;
            align-items: center;
            gap: 14px;

            font-size: clamp(12px, 1.7vh, 14px);
            color: rgba(255,255,255,.92);

            margin-bottom: clamp(8px, 2.2vh, 18px);
        }

        .feature-icon {
            width: clamp(32px, 4.6vh, 38px);
            height: clamp(32px, 4.6vh, 38px);

            flex-shrink: 0;

            display: flex;
            align-items: center;
            justify-content: center;

            background: rgba(255,255,255,.16);
            border-radius: 9px;

            font-size: 14px;
        }

        /* =========================================
           RIGHT PANEL
        ========================================= */

        .login-right {
            width: 50%;
            height: 100%;

            position: relative;
            overflow: hidden;

            display: flex;
            align-items: center;
            justify-content: center;

            background: #ffffff;
        }

        /*
            Decorative background
        */

        .wave {
            position: absolute;
            pointer-events: none;
            opacity: .45;
        }

        .wave-top {
            top: -100px;
            right: -80px;
            width: 600px;
            height: 300px;

            background:
                radial-gradient(
                    ellipse at center,
                    transparent 35%,
                    rgba(37,99,235,.06) 36%,
                    transparent 52%
                );

            transform: rotate(-10deg);
        }

        .wave-bottom {
            bottom: -100px;
            left: -100px;
            width: 600px;
            height: 300px;

            background:
                radial-gradient(
                    ellipse at center,
                    transparent 35%,
                    rgba(37,99,235,.06) 36%,
                    transparent 52%
                );

            transform: rotate(10deg);
        }

        .login-card {
            position: relative;
            z-index: 10;

            width: min(430px, calc(100% - 60px));

            background: rgba(255,255,255,.96);

            border-radius: 22px;

            padding: clamp(24px, 4.6vh, 38px) clamp(22px, 3vw, 34px) clamp(20px, 4vh, 32px);

            box-shadow:
                0 20px 60px rgba(15, 23, 42, .10),
                0 5px 20px rgba(15, 23, 42, .05);

            border: 1px solid rgba(226,232,240,.7);
        }

        /* Card heading */

        .login-title {
            display: flex;
            align-items: center;
            gap: 8px;

            font-size: clamp(22px, 3.2vh, 27px);
            line-height: 1.3;

            font-weight: 700;
            color: #1e293b;

            margin-bottom: 6px;
        }

        .login-title .wave-hand {
            font-size: 25px;
        }

        .login-subtitle {
            font-size: 13px;
            line-height: 1.7;
            color: #7b8794;

            margin-bottom: clamp(16px, 3.6vh, 30px);
        }

        /* Notifikasi sukses (session flash) */

        .flash-success {
            display: flex;
            align-items: flex-start;
            gap: 9px;

            background: #ecfdf3;
            border: 1px solid #b7ecd0;
            border-radius: 11px;

            padding: 11px 14px;
            margin-bottom: 22px;

            font-size: 12px;
            line-height: 1.6;
            color: #12633a;
        }

        .flash-success i {
            margin-top: 2px;
        }

        /* =========================================
           FORM
        ========================================= */

        .form-group {
            margin-bottom: clamp(13px, 2.8vh, 22px);
        }

        .form-label {
            display: block;

            font-size: 12px;
            font-weight: 600;

            color: #273449;

            margin-bottom: clamp(6px, 1.2vh, 9px);
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;

            left: 15px;
            top: 50%;

            transform: translateY(-50%);

            color: #9aa8ba;

            font-size: 15px;

            pointer-events: none;
        }

        .form-input {
            width: 100%;
            height: var(--lf-input-h);

            border: 1px solid #dce2e9;
            border-radius: 12px;

            background: #fbfcfe;

            padding: 0 45px 0 46px;

            font-family: 'Sora', sans-serif;
            font-size: 13px;

            color: #1f2937;

            outline: none;

            transition: all .2s ease;
        }

        .form-input::placeholder {
            color: #a5afbd;
        }

        .form-input:focus {
            background: #ffffff;

            border-color: var(--lf-blue);

            box-shadow:
                0 0 0 3px rgba(37,99,235,.10);
        }

        .form-input.input-error {
            border-color: #dc2626;
            background: #fef6f6;
        }

        .form-input.input-error:focus {
            border-color: #dc2626;
            box-shadow: 0 0 0 3px rgba(220,38,38,.10);
        }

        /* Pesan error di bawah field */

        .field-error {
            display: flex;
            align-items: flex-start;
            gap: 6px;

            margin-top: 7px;
            padding-left: 2px;

            font-size: 11px;
            line-height: 1.55;
            color: #dc2626;
        }

        .field-error i {
            margin-top: 2px;
            font-size: 10px;
        }

        /* Password toggle */

        .password-toggle {
            position: absolute;

            right: 15px;
            top: 50%;

            transform: translateY(-50%);

            border: none;
            background: transparent;

            color: #94a3b8;

            cursor: pointer;

            font-size: 15px;
        }

        .password-toggle:hover {
            color: var(--lf-blue);
        }

        /* =========================================
           CAPTCHA
        ========================================= */

        .captcha-row {
            display: flex;
            align-items: stretch;
            gap: 10px;
        }

        .captcha-row .input-wrapper {
            flex: 1;
        }

        .captcha-row .form-input {
            letter-spacing: .32em;
            text-transform: uppercase;
            font-weight: 700;

            padding-right: 15px;
        }

        .captcha-img {
            height: var(--lf-input-h);
            width: 138px;
            flex-shrink: 0;

            border: 1px solid #dce2e9;
            border-radius: 12px;
            background: #fbfcfe;

            cursor: pointer;

            transition: border-color .2s ease, box-shadow .2s ease;
        }

        .captcha-img:hover {
            border-color: var(--lf-blue);
            box-shadow: 0 0 0 3px rgba(37,99,235,.10);
        }

        .captcha-img.loading {
            opacity: .45;
        }

        .captcha-refresh {
            height: var(--lf-input-h);
            width: 52px;
            flex-shrink: 0;

            border: 1px solid #dce2e9;
            border-radius: 12px;
            background: #fbfcfe;

            color: #7b8794;
            font-size: 15px;

            cursor: pointer;

            transition: all .2s ease;
        }

        .captcha-refresh:hover {
            border-color: var(--lf-blue);
            color: var(--lf-blue);
            background: #f0f5ff;
            transform: rotate(45deg);
        }

        .captcha-hint {
            margin-top: 8px;
            padding-left: 2px;

            font-size: 10.5px;
            color: #a0a9b5;
            line-height: 1.6;
        }

        /* =========================================
           REMEMBER
        ========================================= */

        .form-options {
            display: flex;
            align-items: center;
            justify-content: space-between;

            margin-top: -4px;
            margin-bottom: clamp(14px, 3vh, 25px);
        }

        .remember {
            display: flex;
            align-items: center;
            gap: 8px;

            font-size: 12px;
            font-weight: 600;

            color: #667085;

            cursor: pointer;
        }

        .remember input {
            width: 17px;
            height: 17px;

            cursor: pointer;
            accent-color: var(--lf-blue);
        }

        .secure-note {
            display: inline-flex;
            align-items: center;
            gap: 6px;

            font-size: 11px;
            color: #8a94a3;
        }

        .secure-note i {
            color: #3d9a6c;
        }

        /* =========================================
           LOGIN BUTTON
        ========================================= */

        .login-button {
            width: 100%;
            height: var(--lf-input-h);

            border: none;
            border-radius: 11px;

            background: linear-gradient(
                135deg,
                var(--lf-blue),
                var(--lf-blue-dark)
            );

            color: #ffffff;

            font-family: 'Sora', sans-serif;

            font-size: 13px;
            font-weight: 700;

            cursor: pointer;

            display: flex;
            align-items: center;
            justify-content: center;
            gap: 9px;

            box-shadow:
                0 8px 20px rgba(37,99,235,.20);

            transition: all .2s ease;
        }

        .login-button:hover {
            transform: translateY(-1px);

            box-shadow:
                0 12px 25px rgba(37,99,235,.28);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button:disabled {
            opacity: .85;
            cursor: wait;
        }

        /* =========================================
           FOOTER
        ========================================= */

        .login-help {
            text-align: center;

            margin-top: clamp(14px, 3vh, 26px);
            padding-top: clamp(12px, 2.6vh, 20px);
            border-top: 1px dashed #e6eaf2;

            font-size: 11px;
            color: #7b8794;
            line-height: 1.7;
        }

        .login-help i {
            color: var(--lf-blue);
        }

        .login-footer {
            text-align: center;

            margin-top: clamp(8px, 1.8vh, 14px);

            font-size: 10px;

            color: #a0a9b5;
        }

        /* =========================================
           OVERLAY LOADER LOGIN
        ========================================= */

        #loginLoader {
            position: fixed;
            inset: 0;
            z-index: 2000;

            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 1.1rem;

            background: rgba(15, 23, 42, .82);
            backdrop-filter: blur(7px);

            color: #ffffff;
        }

        #loginLoader.show {
            display: flex;
        }

        .loader-spinner {
            width: 54px;
            height: 54px;
            border-radius: 50%;

            border: 5px solid rgba(255,255,255,.18);
            border-top-color: #8fb2f5;

            animation: spin .8s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        #loginLoaderText {
            font-weight: 600;
            font-size: .95rem;
            letter-spacing: .3px;
        }

        #loginLoaderSub {
            font-size: .78rem;
            color: rgba(255,255,255,.6);
            margin-top: .35rem;
        }

        .loader-dots {
            display: flex;
            gap: 6px;
        }

        .loader-dots span {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #8fb2f5;

            animation: bounceDot 1.2s ease-in-out infinite;
        }

        .loader-dots span:nth-child(2) { animation-delay: .15s; }
        .loader-dots span:nth-child(3) { animation-delay: .3s; }

        @keyframes bounceDot {
            0%, 80%, 100% { transform: scale(.6); opacity: .5; }
            40% { transform: scale(1); opacity: 1; }
        }

        /* =========================================
           RESPONSIVE
        ========================================= */

        @media (max-width: 1000px) {

            .left-content {
                padding: 60px 9%;
            }

            .hero-title {
                font-size: clamp(28px, 2vw + 3vh, 42px);
            }

            .login-card {
                width: calc(100% - 40px);
                padding: 32px 28px;
            }
        }

        @media (max-width: 768px) {

            /* Di layar sempit konten jauh lebih tinggi dari viewport,
               jadi scroll halaman diizinkan kembali di sini. */
            body {
                height: auto;
                overflow: auto;
            }

            .login-wrapper {
                display: block;
                height: auto;
                min-height: 100vh;
                min-height: 100dvh;
                overflow: visible;
            }

            .login-left {
                width: 100%;
                height: auto;
                min-height: 320px;
            }

            .login-right {
                width: 100%;
                height: auto;
                min-height: calc(100vh - 320px);
                min-height: calc(100dvh - 320px);

                padding: 40px 0;
            }

            .left-content {
                padding: 45px 35px;
            }

            .brand {
                margin-bottom: 30px;
            }

            .brand-logo {
                width: 40px;
                height: 40px;
            }

            .brand-name {
                font-size: 17px;
            }

            .hero-title {
                font-size: 34px;
                margin-bottom: 15px;
            }

            .hero-description {
                font-size: 13px;
                line-height: 1.7;
                margin-bottom: 25px;
            }

            .feature {
                font-size: 12px;
            }
        }

        @media (max-width: 480px) {

            .login-left {
                min-height: 290px;
            }

            .login-right {
                min-height: calc(100vh - 290px);
                min-height: calc(100dvh - 290px);
            }

            .left-content {
                padding: 35px 25px;
            }

            .hero-title {
                font-size: 29px;
            }

            .hero-description {
                font-size: 12px;
            }

            .login-card {
                width: calc(100% - 30px);

                padding: 28px 22px;

                border-radius: 18px;
            }

            .login-title {
                font-size: 23px;
            }

            .login-subtitle {
                font-size: 12px;
            }

            /* Captcha: gambar turun ke bawah input agar muat di layar kecil */
            .captcha-row {
                flex-wrap: wrap;
            }

            .captcha-row .input-wrapper {
                flex: 1 1 100%;
            }

            .captcha-img {
                flex: 1;
                width: auto;
            }

            .captcha-refresh {
                flex-shrink: 0;
            }
        }

        /* =========================================
           LAYAR PENDEK (laptop 1366×768 / 1280×720)
           Viewport asli browser biasanya hanya
           ±550–620px, jadi form dipadatkan agar
           seluruh kartu login muat tanpa scroll.
        ========================================= */

        @media (max-height: 820px) {

            /* Pengaman: bila suatu saat konten lebih tinggi
               dari panel, scroll terjadi DI DALAM panel —
               bukan seluruh halaman. */
            .login-right {
                align-items: safe center;
                overflow-y: auto;
                padding-block: 10px;
            }

            /* Panel kiri ikut dipadatkan */

            .left-content {
                padding: 22px 10%;
            }

            .brand {
                margin-bottom: 18px;
            }

            .brand-logo {
                width: 38px;
                height: 38px;
            }

            .brand-name {
                font-size: 16px;
            }

            .hero-title {
                font-size: clamp(26px, 3.4vw + 2vh, 36px);
                margin-bottom: 10px;
            }

            .hero-description {
                font-size: 12px;
                line-height: 1.6;
                margin-bottom: 18px;
            }

            .feature {
                font-size: 11.5px;
                margin-bottom: 7px;
            }

            .feature-icon {
                width: 30px;
                height: 30px;
                font-size: 12px;
            }

            /* Kartu login dipadatkan — muat di ±470px tinggi */

            .login-card {
                padding: 22px 28px 18px;
                border-radius: 16px;
            }

            .login-title {
                font-size: 21px;
                margin-bottom: 3px;
            }

            .login-title .wave-hand {
                font-size: 19px;
            }

            .login-subtitle {
                font-size: 12px;
                margin-bottom: 15px;
            }

            .flash-success {
                padding: 8px 12px;
                margin-bottom: 14px;
                font-size: 11.5px;
            }

            .form-group {
                margin-bottom: 12px;
            }

            .form-label {
                font-size: 11.5px;
                margin-bottom: 5px;
            }

            .form-input {
                height: 42px;
                font-size: 12.5px;
                border-radius: 10px;
            }

            .captcha-img,
            .captcha-refresh {
                height: 42px;
            }

            .captcha-hint {
                margin-top: 5px;
                font-size: 10px;
            }

            .form-options {
                margin-bottom: 15px;
            }

            .remember {
                font-size: 11.5px;
            }

            .login-button {
                height: 42px;
                border-radius: 10px;
                font-size: 12.5px;
            }

            .login-help {
                margin-top: 13px;
                padding-top: 11px;
                font-size: 10.5px;
            }

            .login-footer {
                margin-top: 7px;
                font-size: 9.5px;
            }
        }

    </style>
</head>

<body>

<div class="login-wrapper">

    <!-- =========================================
         LEFT SIDE
    ========================================== -->

    <section class="login-left">

        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>

        <div class="left-content">

            <!-- BRAND -->
            <div class="brand">

                <div class="brand-logo">
                    <img src="{{ company_logo_url() }}" alt="Logo {{ company_name() }}">
                </div>

                <div class="brand-name">
                    {{ company_name() }}
                </div>

            </div>


            <!-- HERO -->
            <h1 class="hero-title">
                Kelola Legal<br>
                Lebih Mudah.
            </h1>

            <p class="hero-description">
                Selamat datang di {{ config('app.name') }}.
                Ajukan perizinan &amp; agreement, pantau proses review,
                dan kelola dokumen legal perusahaan Anda secara
                terpusat dan aman.
            </p>


            <!-- FEATURES -->
            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-file-circle-check"></i>
                </div>

                <span>
                    Pengajuan perizinan &amp; agreement secara terpusat
                </span>

            </div>

            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-clipboard-check"></i>
                </div>

                <span>
                    Review &amp; approval dengan alur terstruktur
                </span>

            </div>

            <div class="feature">

                <div class="feature-icon">
                    <i class="fa-solid fa-shield-halved"></i>
                </div>

                <span>
                    Dokumen legal aman dengan kontrol akses
                </span>

            </div>

        </div>

    </section>


    <!-- =========================================
         RIGHT SIDE
    ========================================== -->

    <section class="login-right">

        <div class="wave wave-top"></div>
        <div class="wave wave-bottom"></div>


        <!-- LOGIN CARD -->

        <div class="login-card">

            <h2 class="login-title">
                Selamat Datang
                <span class="wave-hand">👋</span>
            </h2>

            <p class="login-subtitle">
                Silakan masuk untuk melanjutkan ke akun Anda.
            </p>

            @if (session('success'))
                <div class="flash-success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif


            <!-- LOGIN FORM -->

            <form method="POST" action="{{ route('login.attempt') }}" id="loginForm" novalidate>

                @csrf


                <!-- EMAIL -->

                <div class="form-group">

                    <label class="form-label" for="email">
                        Email
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-envelope input-icon"></i>

                        <input
                            type="email"
                            name="email"
                            id="email"
                            class="form-input @error('email') input-error @enderror"
                            placeholder="nama@perusahaan.com"
                            value="{{ old('email') }}"
                            autocomplete="username"
                            required
                            autofocus
                        >

                    </div>

                    @error('email')
                        <div class="field-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                </div>


                <!-- PASSWORD -->

                <div class="form-group">

                    <label class="form-label" for="password">
                        Password
                    </label>

                    <div class="input-wrapper">

                        <i class="fa-solid fa-lock input-icon"></i>

                        <input
                            type="password"
                            name="password"
                            id="password"
                            class="form-input @error('email') input-error @enderror"
                            placeholder="Masukkan password"
                            autocomplete="current-password"
                            required
                        >

                        <button
                            type="button"
                            class="password-toggle"
                            onclick="togglePassword()"
                            aria-label="Tampilkan password"
                        >
                            <i class="fa-solid fa-eye" id="passwordIcon"></i>
                        </button>

                    </div>

                    @error('email')
                        <div class="field-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                </div>


                <!-- CAPTCHA -->

                <div class="form-group">

                    <label class="form-label" for="captcha">
                        Kode Keamanan
                    </label>

                    <div class="captcha-row">

                        <div class="input-wrapper">

                            <i class="fa-solid fa-shield-halved input-icon"></i>

                            <input
                                type="text"
                                name="captcha"
                                id="captcha"
                                class="form-input @error('captcha') input-error @enderror"
                                placeholder="KODE"
                                maxlength="5"
                                autocomplete="off"
                                autocapitalize="characters"
                                spellcheck="false"
                                required
                            >

                        </div>

                        <img
                            src="{{ route('captcha') }}"
                            alt="Kode keamanan"
                            class="captcha-img"
                            id="captchaImage"
                            title="Klik untuk ganti kode"
                        >

                        <button
                            type="button"
                            class="captcha-refresh"
                            id="captchaRefresh"
                            aria-label="Ganti kode keamanan"
                            title="Ganti kode keamanan"
                        >
                            <i class="fa-solid fa-rotate-right"></i>
                        </button>

                    </div>

                    @error('captcha')
                        <div class="field-error">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>{{ $message }}</span>
                        </div>
                    @enderror

                    <div class="captcha-hint">
                        Masukkan 5 karakter yang tampil pada gambar —
                        klik gambar atau tombol
                        <i class="fa-solid fa-rotate-right"></i>
                        untuk mengganti.
                    </div>

                </div>


                <!-- REMEMBER -->

                <div class="form-options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember"
                            id="remember"
                            value="1"
                            {{ old('remember') ? 'checked' : '' }}
                        >

                        <span>
                            Remember me
                        </span>

                    </label>

                    <span class="secure-note">
                        <i class="fa-solid fa-lock"></i> Koneksi aman
                    </span>

                </div>


                <!-- BUTTON -->

                <button
                    type="submit"
                    class="login-button"
                    id="loginBtn"
                >

                    <i class="fa-solid fa-right-to-bracket"></i>

                    <span>
                        Masuk ke Sistem
                    </span>

                </button>


            </form>


            <!-- HELP -->

            <div class="login-help">
                <i class="fa-solid fa-circle-info"></i>
                Butuh bantuan? Hubungi tim IT / Legal internal perusahaan.
            </div>


            <!-- FOOTER -->

            <div class="login-footer">
                &copy; {{ date('Y') }} {{ company_name() }} — CV Trijaya Solution. All rights reserved.
            </div>

        </div>

    </section>

</div>


<!-- =========================================
     OVERLAY LOADER
========================================== -->

<div id="loginLoader" aria-live="polite">
    <div class="loader-spinner"></div>
    <div>
        <div id="loginLoaderText">Memverifikasi kredensial…</div>
        <div id="loginLoaderSub" style="text-align:center">Sedang menghubungkan ke server</div>
    </div>
    <div class="loader-dots"><span></span><span></span><span></span></div>
</div>


<script>

    // ---------- Toggle ngintip password ----------

    function togglePassword() {

        const password =
            document.getElementById('password');

        const icon =
            document.getElementById('passwordIcon');

        if (password.type === 'password') {

            password.type = 'text';

            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');

        } else {

            password.type = 'password';

            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');

        }

    }


    // ---------- CAPTCHA: muat ulang dengan cache-buster ----------

    const captchaImage = document.getElementById('captchaImage');
    const captchaInput = document.getElementById('captcha');

    function refreshCaptcha() {
        if (!captchaImage) return;
        captchaImage.classList.add('loading');
        // Query unik memastikan browser tidak memakai cache —
        // server meregenerasi kode baru setiap kali gambar diminta.
        captchaImage.src = '{{ route('captcha') }}' + '?t=' + Date.now() + '-' + Math.random().toString(36).slice(2, 7);
        if (captchaInput) captchaInput.value = '';
    }

    captchaImage?.addEventListener('load', () => captchaImage.classList.remove('loading'));
    captchaImage?.addEventListener('error', () => captchaImage.classList.remove('loading'));
    captchaImage?.addEventListener('click', refreshCaptcha);
    document.getElementById('captchaRefresh')?.addEventListener('click', refreshCaptcha);


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
        loginBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i><span>Memproses…</span>';
        loaderText.textContent = 'Memverifikasi kredensial…';
        loaderSub.textContent = 'Sedang menghubungkan ke server';
        loginLoader.classList.add('show');

        // Eskalasi pesan bila proses memakan waktu (server SMTP / session).
        setTimeout(function () {
            loaderText.textContent = 'Menyiapkan dashboard Anda…';
            loaderSub.textContent = 'Mohon tunggu sebentar';
        }, 1300);
    });

    // Bila kembali via tombol back browser (BFCache), pastikan loader tersembunyi
    // dan captcha diambil ulang (kode lama sudah dikonsumsi/hangus di server).
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            loginLoader.classList.remove('show');
            loginBtn.disabled = false;
            loginBtn.innerHTML = '<i class="fa-solid fa-right-to-bracket"></i><span>Masuk ke Sistem</span>';
            refreshCaptcha();
        }
    });

    // Pindah fokus ke captcha otomatis bila ada error captcha.
    @if ($errors->has('captcha'))
        document.getElementById('captcha')?.focus();
    @endif

</script>

</body>
</html>

@php($web = [
    'seo' => website_content('seo'),
    'hero' => website_content('hero'),
    'features' => website_content('features'),
    'workflow' => website_content('workflow'),
    'stats' => website_content('stats'),
    'modules' => website_content('modules'),
    'cta' => website_content('cta'),
    'footer' => website_content('footer'),
])
@php($heroImage = \App\Support\WebsiteContent::heroImageUrl())
@php($formatLead = fn (string $text) => preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', e($text)))
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $web['seo']['title'] }}</title>
    <meta name="description" content="{{ $web['seo']['description'] }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --lf-navy: #0f1e3d;
            --lf-navy-2: #16294f;
            --lf-accent: #2d5da8;
            --lf-accent-2: #4a7fd4;
            --lf-gold: #f5b301;
        }
        * { scroll-behavior: smooth; }
        body { font-family: system-ui, -apple-system, 'Segoe UI', Roboto, sans-serif; overflow-x: hidden; }

        /* ============ NAVBAR LANDING ============ */
        .lp-nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 1030;
            transition: background .3s, box-shadow .3s, padding .3s;
            padding: .9rem 0;
        }
        .lp-nav.scrolled {
            background: rgba(15,30,61,.92); backdrop-filter: blur(12px);
            box-shadow: 0 4px 24px rgba(10,18,38,.25); padding: .55rem 0;
        }
        .lp-nav .nav-link { color: rgba(255,255,255,.82); font-weight: 500; }
        .lp-nav .nav-link:hover, .lp-nav .nav-link.active { color: #fff; }

        /* ============ HERO / BANNER ANIMASI ============ */
        .lp-hero {
            position: relative; min-height: 100vh;
            display: flex; align-items: center;
            background: linear-gradient(125deg, #0f1e3d 0%, #16294f 45%, #2d5da8 100%);
            background-size: 200% 200%;
            animation: gradientShift 14s ease infinite;
            color: #fff; overflow: hidden; padding: 7rem 0 5rem;
        }
        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* blob glow bergerak */
        .lp-blob {
            position: absolute; border-radius: 50%; filter: blur(70px); opacity: .35;
            animation: blobFloat 12s ease-in-out infinite alternate; pointer-events: none;
        }
        .lp-blob.b1 { width: 420px; height: 420px; background: #4a7fd4; top: -120px; right: -80px; }
        .lp-blob.b2 { width: 340px; height: 340px; background: #8d6bd0; bottom: -100px; left: -90px; animation-delay: -4s; }
        .lp-blob.b3 { width: 240px; height: 240px; background: #14a098; top: 40%; left: 55%; animation-delay: -8s; opacity: .22; }
        @keyframes blobFloat {
            0%   { transform: translate(0, 0) scale(1); }
            50%  { transform: translate(40px, -30px) scale(1.12); }
            100% { transform: translate(-30px, 25px) scale(.95); }
        }

        /* grid dots halus */
        .lp-hero::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,.14) 1.2px, transparent 1.2px);
            background-size: 34px 34px; opacity: .5; pointer-events: none;
            mask-image: radial-gradient(ellipse at center, black 30%, transparent 75%);
        }

        .lp-badge {
            display: inline-flex; align-items: center; gap: .5rem;
            background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.22);
            color: #fff; border-radius: 99px; padding: .45rem 1rem;
            font-size: .82rem; font-weight: 600; letter-spacing: .3px;
            backdrop-filter: blur(6px);
            animation: fadeUp .8s ease both;
        }
        .lp-badge .dot {
            width: 8px; height: 8px; border-radius: 50%; background: #22df8a;
            animation: pulseDot 2s ease infinite;
        }
        @keyframes pulseDot {
            0%, 100% { box-shadow: 0 0 0 0 rgba(34,223,138,.6); }
            50% { box-shadow: 0 0 0 7px rgba(34,223,138,0); }
        }

        .lp-hero h1 {
            font-weight: 800; line-height: 1.12; letter-spacing: -.5px;
            font-size: clamp(2.1rem, 5.2vw, 3.6rem);
            animation: fadeUp .8s .15s ease both;
        }
        .lp-hero h1 .grad {
            background: linear-gradient(90deg, #ffd166, #f5b301, #ff9e5e, #ffd166);
            background-size: 300% 100%;
            -webkit-background-clip: text; background-clip: text; color: transparent;
            animation: gradientShift 6s linear infinite;
        }
        .lp-hero .lead-text {
            color: rgba(255,255,255,.85); font-size: 1.06rem; max-width: 540px;
            animation: fadeUp .8s .3s ease both;
        }
        .lp-hero .btn-group-cta { animation: fadeUp .8s .45s ease both; }

        /* kursor typing */
        .type-cursor {
            display: inline-block; width: 3px; height: 1em; margin-left: 3px;
            background: var(--lf-gold); vertical-align: -.12em;
            animation: blink .8s step-end infinite;
        }
        @keyframes blink { 50% { opacity: 0; } }

        /* ---- floating cards di kanan hero ---- */
        .lp-float-wrap { position: relative; height: 440px; animation: fadeUp .9s .4s ease both; }
        .lp-fcard {
            position: absolute; background: rgba(255,255,255,.97); color: #22304d;
            border-radius: 1.1rem; padding: 1rem 1.15rem; box-shadow: 0 22px 55px rgba(8,15,35,.4);
            width: min(300px, 78vw); backdrop-filter: blur(4px);
        }
        .lp-fcard .fc-icon {
            width: 42px; height: 42px; border-radius: .8rem;
            display: inline-flex; align-items: center; justify-content: center; font-size: 1.25rem;
        }
        .lp-fc-1 { top: 12px; left: 4%; animation: floatY 5.5s ease-in-out infinite; }
        .lp-fc-2 { top: 158px; right: 2%; animation: floatY 6.5s .8s ease-in-out infinite; }
        .lp-fc-3 { bottom: 8px; left: 12%; animation: floatY 5s 1.4s ease-in-out infinite; }
        @keyframes floatY {
            0%, 100% { transform: translateY(0) rotate(-.4deg); }
            50% { transform: translateY(-16px) rotate(.6deg); }
        }
        .lp-progress { height: 7px; border-radius: 99px; background: #e7ecf5; overflow: hidden; }
        .lp-progress > div { height: 100%; border-radius: 99px; width: 0; animation: fillBar 2.2s .9s ease forwards; }
        @keyframes fillBar { to { width: var(--w); } }

        /* badge status kecil melayang */
        .lp-chip {
            position: absolute; border-radius: 99px; font-size: .74rem; font-weight: 700;
            padding: .4rem .85rem; box-shadow: 0 10px 26px rgba(8,15,35,.35);
            animation: floatY 4.5s ease-in-out infinite;
        }
        .chip-approve { top: -6px; right: 14%; background: #22a04d; color: #fff; }
        .chip-review  { bottom: 120px; left: -12px; background: #fff; color: #1d3e7a; animation-delay: 1s; }

        /* scroll indicator */
        .lp-scroll-hint {
            position: absolute; bottom: 22px; left: 50%; transform: translateX(-50%);
            color: rgba(255,255,255,.65); font-size: .78rem; text-decoration: none;
            display: flex; flex-direction: column; align-items: center; gap: .3rem;
        }
        .lp-scroll-hint i { animation: bounceDown 1.8s ease infinite; font-size: 1.1rem; }
        @keyframes bounceDown { 0%,100% { transform: translateY(0);} 50% { transform: translateY(7px);} }

        /* ============ SECTION UMUM ============ */
        section { padding: 5.5rem 0; }
        .lp-eyebrow {
            color: var(--lf-accent); font-weight: 700; font-size: .8rem;
            text-transform: uppercase; letter-spacing: .16em;
        }
        .lp-title { font-weight: 800; color: var(--lf-navy); font-size: clamp(1.6rem, 3.4vw, 2.3rem); letter-spacing: -.4px; }

        /* reveal on scroll */
        .reveal { opacity: 0; transform: translateY(34px); transition: opacity .7s ease, transform .7s ease; }
        .reveal.show { opacity: 1; transform: none; }
        .reveal.d1 { transition-delay: .12s; } .reveal.d2 { transition-delay: .24s; } .reveal.d3 { transition-delay: .36s; }

        /* ============ FITUR ============ */
        .feat-card {
            border: none; border-radius: 1.25rem; height: 100%;
            background: #fff; box-shadow: 0 4px 22px rgba(20,33,61,.07);
            transition: transform .3s ease, box-shadow .3s ease;
        }
        .feat-card:hover { transform: translateY(-8px); box-shadow: 0 18px 44px rgba(20,33,61,.14); }
        .feat-icon {
            width: 58px; height: 58px; border-radius: 1rem; font-size: 1.6rem;
            display: inline-flex; align-items: center; justify-content: center; margin-bottom: 1rem;
        }
        .fi-blue   { background: #e8eef9; color: #1d3e7a; }
        .fi-green  { background: #e3f6ec; color: #15803d; }
        .fi-amber  { background: #fdf3dd; color: #b45309; }
        .fi-purple { background: #efe9fa; color: #6a4c93; }
        .feat-list { list-style: none; padding: 0; margin: 0; font-size: .9rem; color: #46536e; }
        .feat-list li { padding: .28rem 0; }
        .feat-list i { color: #22a04d; margin-right: .45rem; }

        /* ============ WORKFLOW ============ */
        .wf-step { position: relative; text-align: center; padding: 0 .5rem; }
        .wf-num {
            width: 64px; height: 64px; border-radius: 50%; margin: 0 auto 1rem;
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
            background: linear-gradient(135deg, var(--lf-accent), var(--lf-accent-2)); color: #fff;
            box-shadow: 0 10px 26px rgba(45,93,168,.35); position: relative; z-index: 2;
        }
        .wf-step::after {
            content: ''; position: absolute; top: 32px; left: calc(50% + 42px); right: calc(-50% + 42px);
            border-top: 2px dashed #c4cfe3;
        }
        .wf-step:last-child::after { display: none; }
        @media (max-width: 767.98px) { .wf-step::after { display: none; } }

        /* ============ STATS COUNTER ============ */
        .lp-stats {
            background: linear-gradient(125deg, var(--lf-navy), var(--lf-navy-2) 60%, var(--lf-accent));
            color: #fff; padding: 4rem 0; position: relative; overflow: hidden;
        }
        .lp-stats::before {
            content: ''; position: absolute; inset: 0;
            background-image: radial-gradient(rgba(255,255,255,.12) 1px, transparent 1px);
            background-size: 26px 26px;
        }
        .stat-box h3 { font-size: clamp(1.9rem, 4vw, 2.8rem); font-weight: 800; margin: 0; }
        .stat-box p { opacity: .8; margin: 0; font-size: .9rem; }

        /* ============ CTA ============ */
        .cta-card {
            border-radius: 1.6rem; padding: 3.2rem 2rem; text-align: center; color: #fff;
            background: linear-gradient(120deg, var(--lf-navy), var(--lf-accent));
            position: relative; overflow: hidden;
        }
        .cta-card::after {
            content: ''; position: absolute; width: 300px; height: 300px; border-radius: 50%;
            background: rgba(255,255,255,.1); top: -140px; right: -100px;
            animation: blobFloat 10s ease-in-out infinite alternate;
        }

        footer { background: var(--lf-navy); color: rgba(255,255,255,.65); padding: 2.6rem 0 1.8rem; }

        /* tombol */
        .btn-gold {
            background: linear-gradient(135deg, #ffd166, #f5b301); color: #17233f;
            font-weight: 700; border: none; border-radius: 99px; padding: .8rem 1.9rem;
            box-shadow: 0 10px 26px rgba(245,179,1,.35); transition: transform .2s, box-shadow .2s;
        }
        .btn-gold:hover { transform: translateY(-3px); box-shadow: 0 16px 34px rgba(245,179,1,.45); color: #17233f; }
        .btn-ghost {
            border: 1.5px solid rgba(255,255,255,.5); color: #fff; border-radius: 99px;
            padding: .8rem 1.9rem; font-weight: 600; transition: background .2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,.12); color: #fff; }

        @keyframes fadeUp { from { opacity: 0; transform: translateY(28px);} to { opacity: 1; transform: none;} }

        @media (max-width: 991.98px) {
            .lp-float-wrap { height: 380px; margin-top: 2.5rem; }
            .lp-fc-2 { right: 0; }
        }
        @media (max-width: 575.98px) {
            .lp-hero { padding-top: 5.5rem; }
            .lp-float-wrap { height: 330px; }
            .lp-fcard { width: min(260px, 84vw); padding: .85rem 1rem; }
            .lp-fc-2 { top: 130px; }
            .chip-review { bottom: 100px; }
        }
    </style>
</head>
<body>

{{-- ============ NAVBAR ============ --}}
<nav class="lp-nav" id="lpNav">
    <div class="container d-flex align-items-center justify-content-between">
        <a class="d-flex align-items-center gap-2 text-white text-decoration-none" href="{{ url('/') }}">
            <img src="{{ company_logo_url() }}" alt="Logo" style="width:38px;height:38px;border-radius:.6rem;">
            <span class="fw-bold fs-5">{{ company_name() }}</span>
        </a>
        <div class="d-none d-md-flex align-items-center gap-4">
            <a class="nav-link" href="#fitur">Fitur</a>
            <a class="nav-link" href="#alur">Cara Kerja</a>
            <a class="nav-link" href="#modul">Modul</a>
        </div>
        <div class="d-flex gap-2">
            @auth
                <a href="{{ route('dashboard') }}" class="btn btn-gold btn-sm px-3 py-2">
                    <i class="bi bi-speedometer2 me-1"></i> Dashboard
                </a>
            @endauth
            @guest
                <a href="{{ route('login') }}" class="btn btn-ghost btn-sm px-3 py-2">Masuk</a>
                <a href="{{ route('login') }}" class="btn btn-gold btn-sm px-3 py-2 d-none d-sm-inline-block">
                    Coba Demo <i class="bi bi-arrow-right ms-1"></i>
                </a>
            @endguest
        </div>
    </div>
</nav>

{{-- ============ HERO / BANNER ============ --}}
<header class="lp-hero" id="hero">
    <div class="lp-blob b1"></div>
    <div class="lp-blob b2"></div>
    <div class="lp-blob b3"></div>

    <div class="container position-relative">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <span class="lp-badge"><span class="dot"></span> {{ $web['hero']['badge'] }}</span>
                <h1 class="mt-4">
                    {{ $web['hero']['title_start'] }} <span class="grad" id="typeText">{{ collect(explode(',', $web['hero']['typing_words']))->filter()->first() }}</span><span class="type-cursor"></span><br>
                    {{ $web['hero']['title_end'] }}
                </h1>
                <p class="lead-text mt-3">{!! $formatLead($web['hero']['lead']) !!}</p>
                <div class="btn-group-cta d-flex flex-wrap gap-3 mt-4">
                    <a href="{{ route('login') }}" class="btn btn-gold">
                        <i class="bi bi-rocket-takeoff me-2"></i>{{ $web['hero']['cta_primary'] }}
                    </a>
                    <a href="#alur" class="btn btn-ghost">
                        <i class="bi bi-play-circle me-2"></i>{{ $web['hero']['cta_secondary'] }}
                    </a>
                </div>
                <div class="d-flex flex-wrap gap-4 mt-4 pt-2" style="font-size:.82rem;color:rgba(255,255,255,.75)">
                    @foreach (array_slice($web['hero']['highlights'], 0, 4) as $highlight)
                        <span><i class="bi bi-shield-check me-1 text-success"></i>{{ $highlight }}</span>
                    @endforeach
                </div>
            </div>

            <div class="col-lg-6">
                @if ($heroImage)
                    <div class="lp-float-wrap d-flex align-items-center justify-content-center" style="height:auto;min-height:320px">
                        <img src="{{ $heroImage }}" alt="Ilustrasi {{ company_name() }}"
                             class="img-fluid rounded-4 shadow-lg" style="max-height:420px;animation:fadeUp .9s .4s ease both">
                    </div>
                @else
                <div class="lp-float-wrap">
                    <span class="lp-chip chip-approve"><i class="bi bi-check-circle me-1"></i>APPROVED</span>
                    <span class="lp-chip chip-review"><i class="bi bi-search me-1"></i>Under Review</span>

                    <div class="lp-fcard lp-fc-1">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fc-icon fi-blue"><i class="bi bi-file-earmark-text"></i></span>
                            <div>
                                <div class="fw-bold" style="font-size:.92rem">Permohonan NIB Cabang Bandung</div>
                                <div class="text-muted small">LF-PRM-2026-0001 · Perizinan Usaha</div>
                            </div>
                        </div>
                        <div class="lp-progress mt-3"><div style="--w:86%;background:linear-gradient(90deg,#2d5da8,#4a7fd4)"></div></div>
                        <div class="d-flex justify-content-between small text-muted mt-1">
                            <span>Kelengkapan dokumen</span><span class="fw-semibold text-dark">86%</span>
                        </div>
                    </div>

                    <div class="lp-fcard lp-fc-2">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fc-icon fi-green"><i class="bi bi-file-earmark-richtext"></i></span>
                            <div>
                                <div class="fw-bold" style="font-size:.92rem">Agreement Pengadaan 2026</div>
                                <div class="text-muted small">LF-AGR-2026-0002 · Rp 2,5 M</div>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-3 flex-wrap">
                            <span class="badge bg-success rounded-pill">Lolos Assessment</span>
                            <span class="badge bg-warning text-dark rounded-pill">Low Risk</span>
                            <span class="badge bg-light text-dark rounded-pill">Score 87.5</span>
                        </div>
                    </div>

                    <div class="lp-fcard lp-fc-3">
                        <div class="d-flex align-items-center gap-3">
                            <span class="fc-icon fi-amber"><i class="bi bi-folder-check"></i></span>
                            <div class="flex-grow-1">
                                <div class="fw-bold" style="font-size:.92rem">Dokumen Terbit</div>
                                <div class="text-muted small">Document Management / Divisi / Perizinan</div>
                            </div>
                        </div>
                        <div class="lp-progress mt-3"><div style="--w:100%;background:linear-gradient(90deg,#15803d,#22a04d)"></div></div>
                        <div class="small text-muted mt-1">Tersimpan otomatis ke folder divisi ✅</div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <a href="#fitur" class="lp-scroll-hint">{{ $web['hero']['scroll_hint'] }} <i class="bi bi-chevron-double-down"></i></a>
</header>

{{-- ============ FITUR UTAMA ============ --}}
<section id="fitur" class="bg-white">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <span class="lp-eyebrow">{{ $web['features']['eyebrow'] }}</span>
            <h2 class="lp-title mt-2">{{ $web['features']['title'] }}</h2>
            <p class="text-muted mx-auto" style="max-width:620px">
                {{ $web['features']['subtitle'] }}
            </p>
        </div>

        <div class="row g-4">
            @foreach ($web['features']['items'] as $i => $feature)
                <div class="col-md-6 col-lg-4 reveal d{{ ($i % 3) + 1 }}">
                    <div class="feat-card p-4">
                        <span class="feat-icon fi-{{ $feature['color'] }}"><i class="{{ $feature['icon'] }}"></i></span>
                        <h5 class="fw-bold">{{ $feature['title'] }}</h5>
                        <ul class="feat-list mt-3">
                            @foreach (preg_split('/\r\n|\r|\n/', $feature['points']) as $point)
                                @if (trim($point) !== '')
                                    <li><i class="bi bi-check-circle-fill"></i>{{ trim($point) }}</li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ ALUR / WORKFLOW ============ --}}
<section id="alur" style="background:var(--lf-bg)">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <span class="lp-eyebrow">{{ $web['workflow']['eyebrow'] }}</span>
            <h2 class="lp-title mt-2">{{ $web['workflow']['title'] }}</h2>
        </div>

        <div class="row g-4">
            @foreach ($web['workflow']['steps'] as $i => $step)
                <div class="col-6 col-md-4 col-lg-{{ min(3, max(2, intdiv(12, min(count($web['workflow']['steps']), 6)))) }} reveal d{{ ($i % 3) + 1 }}">
                    <div class="wf-step">
                        <div class="wf-num" @if ($step['accent'] !== 'blue') style="background:linear-gradient(135deg,{{ $step['accent'] === 'green' ? '#15803d,#22a04d' : '#b45309,#d97706' }});box-shadow:0 10px 26px {{ $step['accent'] === 'green' ? 'rgba(34,160,77,.35)' : 'rgba(217,119,6,.35)' }}" @endif>
                            <i class="{{ $step['icon'] }}"></i>
                        </div>
                        <div class="fw-bold" style="font-size:.92rem">{{ $step['title'] }}</div>
                        <div class="text-muted small">{{ $step['desc'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ STATS ============ --}}
<div class="lp-stats">
    <div class="container position-relative">
        <div class="row text-center g-4">
            @foreach ($web['stats']['items'] as $i => $stat)
                <div class="col-6 col-lg-3 reveal d{{ ($i % 3) + 1 }}">
                    <div class="stat-box">
                        <h3><span class="counter" data-target="{{ (int) filter_var($stat['value'], FILTER_SANITIZE_NUMBER_INT) }}">{{ (int) filter_var($stat['value'], FILTER_SANITIZE_NUMBER_INT) }}</span>{{ $stat['suffix'] }}</h3>
                        <p>{{ $stat['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ============ MODUL ============ --}}
<section id="modul" class="bg-white">
    <div class="container">
        <div class="text-center mb-5 reveal">
            <span class="lp-eyebrow">{{ $web['modules']['eyebrow'] }}</span>
            <h2 class="lp-title mt-2">{{ $web['modules']['title'] }}</h2>
        </div>
        <div class="row g-4">
            @foreach ($web['modules']['items'] as $i => $module)
                <div class="col-lg-4 reveal d{{ $i + 1 }}">
                    <div class="feat-card p-4 h-100">
                        <span class="feat-icon fi-{{ $module['color'] }}"><i class="{{ $module['icon'] }}"></i></span>
                        <h5 class="fw-bold">{{ $module['title'] }}</h5>
                        <p class="text-muted small">{{ $module['desc'] }}</p>
                        <div class="d-flex flex-wrap gap-1">
                            @foreach (array_filter(array_map('trim', explode(',', (string) $module['tags']))) as $tag)
                                <span class="badge bg-light text-dark">{{ $tag }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

{{-- ============ CTA ============ --}}
<section style="padding-top:1rem">
    <div class="container">
        <div class="cta-card reveal">
            <h2 class="fw-bold" style="font-size:clamp(1.5rem,3.4vw,2.2rem)">{{ $web['cta']['title'] }}</h2>
            <p class="mx-auto mt-2" style="max-width:520px;opacity:.85">
                {{ $web['cta']['text'] }}
            </p>
            <a href="{{ route('login') }}" class="btn btn-gold mt-3">
                <i class="bi bi-box-arrow-in-right me-2"></i>{{ $web['cta']['button'] }}
            </a>
        </div>
    </div>
</section>

{{-- ============ FOOTER ============ --}}
<footer>
    <div class="container">
        <div class="row g-4 align-items-center">
            <div class="col-md-6">
                <div class="d-flex align-items-center gap-2 text-white fw-bold fs-5">
                    <img src="{{ company_logo_url() }}" alt="Logo" style="width:34px;height:34px;border-radius:.55rem;">
                    {{ company_name() }}
                </div>
                <div class="small mt-2">{{ $web['footer']['tagline'] }}</div>
            </div>
            <div class="col-md-6 text-md-end small">
                <div>{{ str_replace(':year', date('Y'), $web['footer']['copyright']) }}</div>
                <a href="{{ route('login') }}" class="text-decoration-none" style="color:rgba(255,255,255,.75)">Masuk Aplikasi</a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ===== Navbar berubah saat scroll =====
    const nav = document.getElementById('lpNav');
    const onScroll = () => nav.classList.toggle('scrolled', window.scrollY > 40);
    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // ===== Typing effect di headline (kata dari Kelola Website) =====
    const words = @json(collect(explode(',', $web['hero']['typing_words']))->map(fn ($w) => trim($w))->filter()->values()->whenEmpty(fn ($c) => collect(['LegalFlow']))->all());
    const typeEl = document.getElementById('typeText');
    let wordIndex = 0, charIndex = 0, deleting = false;
    function typeLoop() {
        const word = words[wordIndex];
        typeEl.textContent = word.slice(0, charIndex);
        if (!deleting && charIndex < word.length) {
            charIndex++; setTimeout(typeLoop, 90);
        } else if (deleting && charIndex > 0) {
            charIndex--; setTimeout(typeLoop, 45);
        } else if (!deleting) {
            deleting = true; setTimeout(typeLoop, 1800);
        } else {
            deleting = false; wordIndex = (wordIndex + 1) % words.length; setTimeout(typeLoop, 350);
        }
    }
    typeLoop();

    // ===== Reveal on scroll =====
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('show'); revealObserver.unobserve(e.target); } });
    }, { threshold: 0.14 });
    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    // ===== Counter animation =====
    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (!e.isIntersecting) return;
            const el = e.target, target = +el.dataset.target, duration = 1400;
            const start = performance.now();
            const tick = (now) => {
                const p = Math.min((now - start) / duration, 1);
                el.textContent = Math.round(target * (1 - Math.pow(1 - p, 3))); // ease-out cubic
                if (p < 1) requestAnimationFrame(tick);
            };
            requestAnimationFrame(tick);
            counterObserver.unobserve(el);
        });
    }, { threshold: 0.5 });
    document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));

    // ===== Parallax halus blob mengikuti mouse (desktop) =====
    if (window.matchMedia('(min-width: 992px)').matches) {
        const blobs = document.querySelectorAll('.lp-blob');
        document.querySelector('.lp-hero').addEventListener('mousemove', (e) => {
            const x = (e.clientX / window.innerWidth - .5), y = (e.clientY / window.innerHeight - .5);
            blobs.forEach((b, i) => {
                const depth = (i + 1) * 14;
                b.style.translate = `${x * depth}px ${y * depth}px`;
            });
        });
    }
</script>
</body>
</html>

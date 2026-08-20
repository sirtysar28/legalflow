<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — {{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --lf-navy: #0f1e3d;
            --lf-navy-2: #16294f;
            --lf-accent: #2d5da8;
            --lf-accent-2: #4a7fd4;
            --lf-bg: #f2f5fa;
            --lf-sidebar-w: 264px;
            --lf-sidebar-w-min: 78px;
            --lf-topbar-h: 64px;
        }
        html { scroll-behavior: smooth; }
        body { background: var(--lf-bg); min-height: 100vh; overflow-x: hidden; }

        /* ================= SIDEBAR ================= */
        .lf-sidebar {
            position: fixed; inset-block: 0; left: 0; z-index: 1045;
            width: var(--lf-sidebar-w);
            background: linear-gradient(180deg, var(--lf-navy) 0%, var(--lf-navy-2) 100%);
            display: flex; flex-direction: column;
            transition: width .25s ease, transform .3s ease;
        }
        .lf-sidebar .lf-brand {
            display: flex; align-items: center; gap: .75rem;
            padding: 1rem 1.1rem; color: #fff; text-decoration: none;
            border-bottom: 1px solid rgba(255,255,255,.08); min-height: var(--lf-topbar-h);
        }
        .lf-sidebar .lf-logo {
            width: 40px; height: 40px; object-fit: cover; border-radius: .65rem; background: #fff; flex-shrink: 0;
        }
        .lf-sidebar .lf-brand-name { font-weight: 800; font-size: 1.15rem; letter-spacing: .3px; white-space: nowrap; }
        .lf-sidebar .lf-brand-sub { font-size: .68rem; opacity: .6; white-space: nowrap; }

        .lf-nav { flex: 1 1 auto; overflow-y: auto; overflow-x: hidden; padding: .85rem .7rem; }
        .lf-nav::-webkit-scrollbar { width: 5px; }
        .lf-nav::-webkit-scrollbar-thumb { background: rgba(255,255,255,.15); border-radius: 99px; }

        .lf-nav .lf-section {
            font-size: .66rem; text-transform: uppercase; letter-spacing: .12em;
            color: rgba(255,255,255,.42); padding: 1rem .75rem .35rem; white-space: nowrap;
        }
        .lf-sidebar a.lf-link {
            display: flex; align-items: center; gap: .8rem;
            color: rgba(255,255,255,.72); text-decoration: none;
            padding: .6rem .8rem; border-radius: .6rem; margin-bottom: .15rem;
            font-size: .9rem; font-weight: 500; white-space: nowrap;
            transition: background .15s, color .15s;
        }
        .lf-sidebar a.lf-link i { font-size: 1.1rem; width: 1.35rem; text-align: center; flex-shrink: 0; }
        .lf-sidebar a.lf-link:hover { color: #fff; background: rgba(255,255,255,.08); }
        .lf-sidebar a.lf-link.active {
            color: #fff; background: linear-gradient(90deg, var(--lf-accent), var(--lf-accent-2));
            box-shadow: 0 4px 14px rgba(45,93,168,.45);
        }
        .lf-sidebar .lf-link .lf-badge {
            margin-left: auto; font-size: .65rem;
            background: #dc3545; color: #fff; border-radius: 99px; padding: .15rem .5rem;
        }

        .lf-sidebar .lf-side-footer {
            padding: .8rem .7rem; border-top: 1px solid rgba(255,255,255,.08);
        }

        /* ============ COLLAPSED (desktop minimize) ============ */
        body.lf-collapsed .lf-sidebar { width: var(--lf-sidebar-w-min); }
        body.lf-collapsed .lf-sidebar .lf-brand { justify-content: center; padding-inline: .5rem; }
        body.lf-collapsed .lf-sidebar .lf-brand-text { display: none; }
        body.lf-collapsed .lf-sidebar .lf-label,
        body.lf-collapsed .lf-sidebar .lf-section { display: none; }
        body.lf-collapsed .lf-sidebar a.lf-link { justify-content: center; padding-inline: .5rem; }
        body.lf-collapsed .lf-sidebar a.lf-link .lf-badge { position: absolute; transform: translate(14px,-8px); margin: 0; }
        body.lf-collapsed .lf-sidebar a.lf-link { position: relative; }
        body.lf-collapsed .lf-main { margin-left: var(--lf-sidebar-w-min); }

        /* ============ MAIN ============ */
        .lf-main { margin-left: var(--lf-sidebar-w); transition: margin .25s ease; min-height: 100vh; display: flex; flex-direction: column; }
        .lf-topbar {
            position: sticky; top: 0; z-index: 1020; min-height: var(--lf-topbar-h);
            background: rgba(255,255,255,.92); backdrop-filter: blur(10px);
            border-bottom: 1px solid #e6eaf2;
            display: flex; align-items: center; gap: .5rem; padding: .5rem .85rem;
        }
        .lf-main > main { flex: 1 1 auto; padding: 1.25rem .85rem; }
        @media (min-width: 768px) { .lf-main > main { padding: 1.5rem 1.75rem; } }
        .lf-page-title { font-weight: 800; color: var(--lf-navy); }
        @media (max-width: 575.98px) {
            .lf-page-title { font-size: 1rem; max-width: 42vw; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        }

        .lf-toggle-btn {
            border: none; background: transparent; font-size: 1.3rem; color: var(--lf-navy);
            padding: .35rem .55rem; border-radius: .55rem; line-height: 1;
        }
        .lf-toggle-btn:hover { background: #eef2f9; }

        /* Notifikasi: bel bergetar saat ada notifikasi baru */
        @keyframes bellRing {
            0%, 100% { transform: rotate(0); }
            10% { transform: rotate(12deg); }
            20% { transform: rotate(-12deg); }
            30% { transform: rotate(8deg); }
            40% { transform: rotate(-8deg); }
            50% { transform: rotate(4deg); }
            60% { transform: rotate(-4deg); }
        }
        .lf-bell-ring i { animation: bellRing 2.4s ease-in-out infinite; color: #d97706; }

        /* Item notifikasi */
        .lf-notif-item { padding: .7rem .9rem; border-bottom: 1px solid #f0f3f8; transition: background .15s; }
        .lf-notif-item:hover { background: #f5f8fd; }
        .lf-notif-icon {
            width: 36px; height: 36px; border-radius: .7rem; flex-shrink: 0;
            display: inline-flex; align-items: center; justify-content: center; font-size: 1.05rem;
        }
        .ni-blue  { background: #e8eef9; color: #1d3e7a; }
        .ni-green { background: #e3f6ec; color: #15803d; }
        .ni-amber { background: #fdf3dd; color: #b45309; }
        .ni-red   { background: #fdecec; color: #b91c1c; }

        /* ============ MOBILE ============ */
        .lf-backdrop {
            position: fixed; inset: 0; background: rgba(10,18,38,.55); z-index: 1040;
            opacity: 0; pointer-events: none; transition: opacity .25s;
        }
        @media (max-width: 991.98px) {
            .lf-sidebar { transform: translateX(-100%); width: var(--lf-sidebar-w) !important; }
            body.lf-sidebar-open .lf-sidebar { transform: translateX(0); box-shadow: 0 0 40px rgba(0,0,0,.35); }
            body.lf-sidebar-open .lf-backdrop { opacity: 1; pointer-events: auto; }
            .lf-main { margin-left: 0 !important; }
        }

        /* ============ KOMPONEN ============ */
        .lf-card { border: none; border-radius: 1rem; box-shadow: 0 2px 10px rgba(20,33,61,.06); transition: box-shadow .2s ease; }
        .lf-card:hover { box-shadow: 0 6px 22px rgba(20,33,61,.10); }

        /* Tombol & form lebih modern */
        .btn { border-radius: .55rem; font-weight: 600; transition: transform .12s ease, box-shadow .2s ease, background .2s ease; }
        .btn:not(:disabled):hover { transform: translateY(-1px); }
        .btn:active { transform: translateY(0); }
        .btn-primary {
            background: linear-gradient(135deg, var(--lf-navy-2), var(--lf-accent)); border: none;
            box-shadow: 0 4px 12px rgba(45,93,168,.28);
        }
        .btn-primary:hover, .btn-primary:focus { background: linear-gradient(135deg, var(--lf-navy-2), var(--lf-accent)); filter: brightness(1.12); box-shadow: 0 6px 16px rgba(45,93,168,.38); }
        .btn-outline-primary { color: var(--lf-accent); border-color: var(--lf-accent); }
        .btn-outline-primary:hover { background: var(--lf-accent); border-color: var(--lf-accent); }
        .btn-success { background: linear-gradient(135deg,#15803d,#22a04d); border: none; box-shadow: 0 4px 12px rgba(21,128,61,.25); }
        .btn-success:hover { background: linear-gradient(135deg,#15803d,#22a04d); filter: brightness(1.1); }
        .btn-danger { background: linear-gradient(135deg,#b91c1c,#dc2626); border: none; box-shadow: 0 4px 12px rgba(185,28,28,.25); }
        .btn-danger:hover { background: linear-gradient(135deg,#b91c1c,#dc2626); filter: brightness(1.1); }
        .btn-warning { background: linear-gradient(135deg,#b45309,#d97706); border: none; color: #fff; }
        .btn-warning:hover { background: linear-gradient(135deg,#b45309,#d97706); color: #fff; filter: brightness(1.08); }

        .form-control, .form-select { border-radius: .55rem; border-color: #dde3ee; }
        .form-control:focus, .form-select:focus { border-color: var(--lf-accent-2); box-shadow: 0 0 0 .2rem rgba(74,127,212,.18); }
        .form-check-input:checked { background-color: var(--lf-accent); border-color: var(--lf-accent); }
        .form-check-input:focus { box-shadow: 0 0 0 .2rem rgba(74,127,212,.18); }
        .input-group-text { border-color: #dde3ee; }
        .input-group > .form-control:not(:last-child), .input-group > .form-select:not(:last-child) { border-top-right-radius: 0; border-bottom-right-radius: 0; }

        /* Tabel lebih rapi */
        .table > thead th {
            background: #f1f4fa; color: #26355c; font-size: .72rem;
            text-transform: uppercase; letter-spacing: .07em; border-bottom: 2px solid #dde3ee;
            white-space: nowrap; padding: .65rem .75rem;
        }
        .table tbody tr { transition: background .12s ease; }
        .table tbody tr:hover { background: #f7f9fd; }

        /* Alert & badge */
        .alert { border-radius: .75rem; border: none; box-shadow: 0 3px 10px rgba(20,33,61,.06); }
        .alert-success { background: #e3f6ec; color: #12633a; border-left: 4px solid #16a34a; }
        .alert-info    { background: #e8eef9; color: #1d3e7a; border-left: 4px solid #2d5da8; }
        .alert-danger  { background: #fdecec; color: #991b1b; border-left: 4px solid #dc2626; }
        .alert-warning { background: #fdf3dd; color: #92400e; border-left: 4px solid #d97706; }
        .badge { border-radius: .45rem; }

        /* Modal & dropdown */
        .modal-content { border-radius: 1rem; border: none; box-shadow: 0 20px 60px rgba(10,18,38,.3); }
        .modal-header { border-bottom: 1px solid #edf0f6; }
        .dropdown-menu { border-radius: .8rem; border-color: #e6eaf2; }

        /* Scrollbar halus */
        ::-webkit-scrollbar { width: 9px; height: 9px; }
        ::-webkit-scrollbar-thumb { background: #c3cddd; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #a8b4cc; }
        ::-webkit-scrollbar-track { background: transparent; }

        ::selection { background: rgba(45,93,168,.25); }

        .lf-stat { border-radius: 1rem; padding: 1.1rem 1.25rem; color: #fff; position: relative; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; }
        .lf-stat h2 { font-weight: 800; margin: 0; }
        .lf-stat p { margin: 0; opacity: .9; font-size: .875rem; }
        .lf-stat:hover { transform: translateY(-3px); box-shadow: 0 10px 24px rgba(20,33,61,.18); }
        .lf-stat::after {
            content: ''; position: absolute; right: -30px; bottom: -30px;
            width: 110px; height: 110px; border-radius: 50%; background: rgba(255,255,255,.12);
        }
        .stat-purple { background: linear-gradient(135deg,#6a4c93,#8d6bd0); }
        .stat-blue   { background: linear-gradient(135deg,#1d3e7a,#2d5da8); }
        .stat-teal   { background: linear-gradient(135deg,#0f766e,#14a098); }
        .stat-amber  { background: linear-gradient(135deg,#b45309,#d97706); }
        .stat-green  { background: linear-gradient(135deg,#15803d,#22a04d); }
        .stat-red    { background: linear-gradient(135deg,#b91c1c,#dc2626); }
        .stat-dark   { background: linear-gradient(135deg,#374151,#4b5563); }

        .table { font-size: .92rem; }
        .table > :not(caption) > * > * { vertical-align: middle; }
        .badge-status { font-size: .75rem; }
        .card-header { border-bottom: 1px solid #edf0f6; }

        .timeline { position: relative; padding-left: 1.75rem; list-style: none; }
        .timeline::before { content: ''; position: absolute; left: .45rem; top: .4rem; bottom: .4rem; width: 2px; background: #dde3ee; }
        .timeline-item { position: relative; padding-bottom: 1rem; }
        .timeline-item::before {
            content: ''; position: absolute; left: -1.45rem; top: .35rem; width: 12px; height: 12px;
            border-radius: 50%; background: var(--lf-accent); border: 2px solid #fff; box-shadow: 0 0 0 2px var(--lf-accent);
        }
        .req-done { color: #15803d; } .req-missing { color: #b91c1c; }

        /* Nav-pills halaman pengaturan: bisa digeser horizontal di mobile */
        .lf-settings-nav { overflow-x: auto; scrollbar-width: none; }
        .lf-settings-nav::-webkit-scrollbar { display: none; }
        .lf-settings-nav .nav-link {
            color: #52617f; font-weight: 600; font-size: .88rem; border-radius: .6rem;
            padding: .5rem .9rem; white-space: nowrap;
        }
        .lf-settings-nav .nav-link.active {
            background: linear-gradient(135deg, var(--lf-navy-2), var(--lf-accent));
            box-shadow: 0 4px 12px rgba(45,93,168,.35);
        }

        /* Scroll tabel lebih nyaman di layar kecil */
        .table-responsive { -webkit-overflow-scrolling: touch; }

        @media (max-width: 767.98px) {
            .table { font-size: .85rem; }
            .table > :not(caption) > * > * { padding: .5rem .55rem; }
            .modal-dialog { margin: .5rem; }
            .btn { font-size: .9rem; }
            .btn .btn-text-sm-none { display: none; }
        }

        @media (max-width: 575.98px) {
            .lf-stat h2 { font-size: 1.4rem; }
            h4 { font-size: 1.05rem; }
            .lf-main > main { padding-bottom: 2rem; }
            .alert { font-size: .88rem; }
        }

        /* ============ LOADER / SPINNER GLOBAL ============ */
        .lf-page-loader {
            position: fixed; inset: 0; z-index: 3000;
            display: flex; align-items: center; justify-content: center;
            background: rgba(244, 247, 252, .94); backdrop-filter: blur(5px);
            transition: opacity .25s ease;
        }
        .lf-page-loader.lf-hide { opacity: 0; pointer-events: none; }
        .lf-page-loader-inner { text-align: center; display: flex; flex-direction: column; align-items: center; gap: .9rem; }
        .lf-spinner {
            width: 52px; height: 52px; border-radius: 50%;
            border: 5px solid #d7e0f0; border-top-color: var(--lf-accent);
            animation: lfSpin .8s linear infinite;
        }
        @keyframes lfSpin { to { transform: rotate(360deg); } }
        .lf-loader-text { font-weight: 700; color: var(--lf-navy); font-size: .92rem; }
        .lf-loader-sub { font-size: .78rem; color: #8a94a6; margin-top: .35rem; }
        .lf-loader-dots { display: flex; gap: 6px; }
        .lf-loader-dots span {
            width: 7px; height: 7px; border-radius: 50%; background: var(--lf-accent-2);
            animation: lfBounceDot 1.2s ease-in-out infinite;
        }
        .lf-loader-dots span:nth-child(2) { animation-delay: .15s; }
        .lf-loader-dots span:nth-child(3) { animation-delay: .3s; }
        @keyframes lfBounceDot {
            0%, 80%, 100% { transform: scale(.6); opacity: .5; }
            40% { transform: scale(1); opacity: 1; }
        }
    </style>
</head>
<body class="@if(auth()->check() && session()->has('lf_collapsed')) @endif">
{{-- ===== Loader global: tampil saat halaman dimuat / pindah halaman ===== --}}
<div class="lf-page-loader" id="lfPageLoader" aria-live="polite">
    <div class="lf-page-loader-inner">
        <div class="lf-spinner"></div>
        <div>
            <div class="lf-loader-text" id="lfLoaderText">Memuat halaman…</div>
            <div class="lf-loader-sub" id="lfLoaderSub">Mohon tunggu sebentar</div>
        </div>
        <div class="lf-loader-dots"><span></span><span></span><span></span></div>
    </div>
</div>
@php($user = auth()->user())

{{-- ================= SIDEBAR ================= --}}
<aside class="lf-sidebar" id="lfSidebar">
    <a class="lf-brand" href="{{ route('dashboard') }}">
        <img src="{{ company_logo_url() }}" alt="Logo {{ company_name() }}" class="lf-logo">
        <span class="lf-brand-text">
            <span class="lf-brand-name d-block">{{ company_name() }}</span>
            <span class="lf-brand-sub">Legal Management Suite</span>
        </span>
    </a>

    <nav class="lf-nav">
        <a href="{{ route('dashboard') }}"
           class="lf-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i><span class="lf-label">Dashboard</span>
        </a>

        @if (! $user->canReview())
            <div class="lf-section">Pengajuan</div>
            <a href="{{ route('applications.index', ['type' => 'PERMIT']) }}"
               class="lf-link {{ request()->routeIs('applications.*') && request('type') === 'PERMIT' ? 'active' : '' }}">
                <i class="bi bi-file-earmark-text"></i><span class="lf-label">Pengajuan Izin</span>
            </a>
            <a href="{{ route('applications.index', ['type' => 'AGREEMENT']) }}"
               class="lf-link {{ request()->routeIs('applications.*') && request('type') === 'AGREEMENT' ? 'active' : '' }}">
                <i class="bi bi-file-earmark-richtext"></i><span class="lf-label">Agreement</span>
            </a>
            <a href="{{ route('applications.create', ['type' => 'PERMIT']) }}" class="lf-link">
                <i class="bi bi-plus-circle"></i><span class="lf-label">Buat Izin Baru</span>
            </a>
            <a href="{{ route('applications.create', ['type' => 'AGREEMENT']) }}" class="lf-link">
                <i class="bi bi-plus-circle"></i><span class="lf-label">Buat Agreement</span>
            </a>
        @else
            <div class="lf-section">Review</div>
            <a href="{{ route('review.queue') }}"
               class="lf-link {{ request()->routeIs('review.queue') ? 'active' : '' }}">
                <i class="bi bi-clipboard-check"></i><span class="lf-label">Antrean Review</span>
                @php($queueCount = \App\Models\Application::whereIn('status', ['SUBMITTED','RESUBMITTED','UNDER_REVIEW'])->count())
                @if ($queueCount > 0)<span class="lf-badge">{{ $queueCount }}</span>@endif
            </a>
            <a href="{{ route('applications.index') }}"
               class="lf-link {{ request()->routeIs('applications.index') && ! request('type') ? 'active' : '' }}">
                <i class="bi bi-stack"></i><span class="lf-label">Semua Pengajuan</span>
            </a>
            <a href="{{ route('access-requests.incoming') }}"
               class="lf-link {{ request()->routeIs('access-requests.incoming') ? 'active' : '' }}">
                <i class="bi bi-person-lock"></i><span class="lf-label">Permintaan Akses</span>
                @php($accessCount = \App\Models\DocumentAccessRequest::where('status', 'ACCESS_REQUESTED')->count())
                @if ($accessCount > 0)<span class="lf-badge">{{ $accessCount }}</span>@endif
            </a>
        @endif

        <div class="lf-section">Dokumen</div>
        <a href="{{ route('documents.browse') }}"
           class="lf-link {{ request()->routeIs('documents.browse') ? 'active' : '' }}">
            <i class="bi bi-folder-check"></i><span class="lf-label">Dokumen Terbit</span>
        </a>
        @if ($user->canReview())
            <a href="{{ route('documents.folders') }}"
               class="lf-link {{ request()->routeIs('documents.folders') ? 'active' : '' }}">
                <i class="bi bi-diagram-2"></i><span class="lf-label">Kelola Folder</span>
            </a>
        @endif
        @if (! $user->canReview())
            <a href="{{ route('access-requests.mine') }}"
               class="lf-link {{ request()->routeIs('access-requests.mine') ? 'active' : '' }}">
                <i class="bi bi-key"></i><span class="lf-label">Request Akses Saya</span>
            </a>
        @endif

        @if ($user->isAdmin())
            <div class="lf-section">Administrasi</div>
            <a href="{{ route('admin.users.index') }}" class="lf-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="bi bi-people"></i><span class="lf-label">Manajemen User</span>
            </a>
            <a href="{{ route('admin.departments.index') }}" class="lf-link {{ request()->routeIs('admin.departments.*') ? 'active' : '' }}">
                <i class="bi bi-diagram-3"></i><span class="lf-label">Divisi</span>
            </a>
            <a href="{{ route('admin.permit-types.index') }}" class="lf-link {{ request()->routeIs('admin.permit-types.*') ? 'active' : '' }}">
                <i class="bi bi-list-check"></i><span class="lf-label">Jenis Izin</span>
            </a>
            <a href="{{ route('admin.requirements.index') }}" class="lf-link {{ request()->routeIs('admin.requirements.*') ? 'active' : '' }}">
                <i class="bi bi-file-earmark-arrow-up"></i><span class="lf-label">Persyaratan Dokumen</span>
            </a>
            <a href="{{ route('admin.suppliers.index') }}" class="lf-link {{ request()->routeIs('admin.suppliers.*') ? 'active' : '' }}">
                <i class="bi bi-truck"></i><span class="lf-label">Supplier</span>
            </a>
            <a href="{{ route('admin.histories.index') }}" class="lf-link {{ request()->routeIs('admin.histories.*') ? 'active' : '' }}">
                <i class="bi bi-clock-history"></i><span class="lf-label">Audit Trail</span>
            </a>
        @endif

        @if ($user->isSuperAdmin())
            <div class="lf-section">Website</div>
            <a href="{{ route('admin.website.index') }}" class="lf-link {{ request()->routeIs('admin.website.*') ? 'active' : '' }}">
                <i class="bi bi-window-desktop"></i><span class="lf-label">Kelola Website</span>
            </a>
        @endif
    </nav>

    <div class="lf-side-footer">
        <a href="{{ route('settings.index') }}"
           class="lf-link {{ request()->routeIs('settings.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i><span class="lf-label">Pengaturan</span>
        </a>
        <a href="{{ route('landing') }}" class="lf-link">
            <i class="bi bi-info-circle"></i><span class="lf-label">Tentang {{ company_name() }}</span>
        </a>
    </div>
</aside>
<div class="lf-backdrop" id="lfBackdrop"></div>

{{-- ================= MAIN ================= --}}
<div class="lf-main">
    <header class="lf-topbar">
        <button class="lf-toggle-btn" id="lfSidebarToggle" type="button" aria-label="Buka/tutup menu">
            <i class="bi bi-list"></i>
        </button>

        <div class="me-auto">
            <div class="lf-page-title">@yield('title', 'Dashboard')</div>
        </div>

        <span class="badge rounded-pill d-none d-md-inline-flex align-items-center gap-1"
              style="background:#e8eef9;color:var(--lf-accent);">
            <i class="bi bi-shield-check"></i> {{ $user->role?->label ?? '' }}
        </span>

        {{-- Notifikasi --}}
        <div class="dropdown">
            @php($unread = $user->unreadNotifications->take(15))
            <button class="lf-toggle-btn position-relative {{ $unread->isNotEmpty() ? 'lf-bell-ring' : '' }}" data-bs-toggle="dropdown" aria-label="Notifikasi">
                <i class="bi bi-bell"></i>
                @if ($unread->isNotEmpty())
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size:.62rem">
                        {{ $unread->count() }}
                    </span>
                @endif
            </button>
            <div class="dropdown-menu dropdown-menu-end shadow p-0" style="width: min(360px, 90vw);">
                <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                    <span class="fw-bold small"><i class="bi bi-bell me-1"></i>Notifikasi ({{ $unread->count() }} baru)</span>
                    @if ($unread->isNotEmpty())
                        <form method="POST" action="{{ route('notifications.read-all') }}">
                            @csrf
                            <button class="btn btn-link btn-sm p-0 text-decoration-none" style="font-size:.72rem">
                                Tandai semua dibaca
                            </button>
                        </form>
                    @endif
                </div>
                <div style="max-height: 380px; overflow-y: auto;">
                    @forelse ($unread as $notification)
                        @php($ntitle = $notification->data['title'] ?? 'Notifikasi')
                        <a href="{{ route('notifications.read', $notification->id) }}"
                           class="dropdown-item text-wrap d-flex gap-2 align-items-start lf-notif-item">
                            <span class="lf-notif-icon {{ str_contains($ntitle, 'Disetujui') || str_contains($ntitle, 'Setujui') || str_contains($ntitle, 'Disetujui') ? 'ni-green' : (str_contains($ntitle, 'Ditolak') ? 'ni-red' : (str_contains($ntitle, 'Revisi') ? 'ni-amber' : 'ni-blue')) }}">
                                <i class="bi {{ str_contains($ntitle, 'Masuk') ? 'bi-inbox' : (str_contains($ntitle, 'Disetujui') ? 'bi-check-circle' : (str_contains($ntitle, 'Ditolak') ? 'bi-x-circle' : (str_contains($ntitle, 'Revisi') ? 'bi-arrow-repeat' : (str_contains($ntitle, 'Akses') ? 'bi-key' : 'bi-bell')))) }}"></i>
                            </span>
                            <span class="flex-grow-1">
                                <span class="d-block fw-semibold small">{{ $ntitle }}</span>
                                <span class="d-block text-muted" style="font-size:.76rem">{{ $notification->data['message'] ?? '' }}</span>
                                <span class="d-block text-secondary mt-1" style="font-size:.7rem">
                                    <i class="bi bi-clock me-1"></i>{{ $notification->created_at->diffForHumans() }}
                                </span>
                            </span>
                            <span class="badge rounded-pill bg-danger flex-shrink-0 mt-1" style="font-size:.6rem">BARU</span>
                        </a>
                    @empty
                        <div class="text-center text-muted small py-4">
                            <i class="bi bi-bell-slash d-block mb-2" style="font-size:1.6rem"></i>
                            Tidak ada notifikasi baru.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Profil --}}
        <div class="dropdown">
            <button class="btn d-flex align-items-center gap-2 border-0 p-1" data-bs-toggle="dropdown">
                <span class="d-inline-flex align-items-center justify-content-center rounded-circle fw-bold text-white"
                      style="width:36px;height:36px;background:linear-gradient(135deg,var(--lf-accent),var(--lf-accent-2));font-size:.9rem;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </span>
                <span class="d-none d-lg-inline text-start" style="line-height:1.1">
                    <span class="d-block fw-semibold" style="font-size:.85rem">{{ $user->name }}</span>
                    <span class="d-block text-muted" style="font-size:.72rem">{{ $user->department?->name ?? 'Tanpa Divisi' }}</span>
                </span>
                <i class="bi bi-chevron-down small text-muted d-none d-lg-inline"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow">
                <li><span class="dropdown-item-text small text-muted">
                    {{ $user->role?->label }} @if($user->department) · {{ $user->department->name }} @endif
                </span></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item" href="{{ route('settings.index') }}">
                        <i class="bi bi-gear me-2"></i>Pengaturan
                    </a>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" data-loader-text="Sedang keluar…" data-loader-sub="Mengakhiri sesi Anda">
                        @csrf
                        <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </header>

    <main>
        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('info'))
            <div class="alert alert-info alert-dismissible fade show">
                <i class="bi bi-info-circle me-1"></i> {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if (session('danger'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-x-octagon me-1"></i> {{ session('danger') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle me-1"></i>
                <strong>Perhatian:</strong>
                <ul class="mb-0 mt-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center text-muted small py-3" style="font-size:.78rem">
        &copy; {{ date('Y') }} LegalFlow — PT Trijaya Solution
    </footer>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ---------- Sidebar: collapse (desktop) & drawer (mobile) ----------
    const body = document.body;
    const toggle = document.getElementById('lfSidebarToggle');
    const backdrop = document.getElementById('lfBackdrop');

    if (localStorage.getItem('lf-collapsed') === '1') body.classList.add('lf-collapsed');

    function isMobile() { return window.matchMedia('(max-width: 991.98px)').matches; }

    toggle.addEventListener('click', function () {
        if (isMobile()) {
            body.classList.toggle('lf-sidebar-open');
        } else {
            body.classList.toggle('lf-collapsed');
            localStorage.setItem('lf-collapsed', body.classList.contains('lf-collapsed') ? '1' : '0');
        }
    });
    backdrop.addEventListener('click', () => body.classList.remove('lf-sidebar-open'));
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') body.classList.remove('lf-sidebar-open');
    });
    window.addEventListener('resize', () => { if (!isMobile()) body.classList.remove('lf-sidebar-open'); });

    // ---------- Loader / spinner global ----------
    const pageLoader = document.getElementById('lfPageLoader');
    const loaderText = document.getElementById('lfLoaderText');
    const loaderSub = document.getElementById('lfLoaderSub');
    let lfNavigating = false;

    function hidePageLoader() { pageLoader.classList.add('lf-hide'); lfNavigating = false; }
    function showPageLoader(text, sub) {
        if (text) loaderText.textContent = text;
        if (sub !== undefined) loaderSub.textContent = sub;
        pageLoader.classList.remove('lf-hide');
        lfNavigating = true;
    }

    // Sembunyikan loader setelah halaman selesai dirender (mis. saat masuk ke dashboard).
    window.addEventListener('load', hidePageLoader);
    setTimeout(hidePageLoader, 3500); // pengaman bila event load terlewat

    // Tampilkan loader saat menekan link internal (pindah antar halaman).
    document.addEventListener('click', function (e) {
        if (e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        const a = e.target.closest('a');
        if (!a) return;
        const href = a.getAttribute('href') || '';
        if (!href || href.startsWith('#') || href.startsWith('mailto:') || href.startsWith('tel:')
            || href.startsWith('javascript:') || a.target === '_blank'
            || a.hasAttribute('download') || a.hasAttribute('data-bs-toggle')
            || a.origin !== location.origin) return;
        if (!lfNavigating) showPageLoader('Memuat…', 'Membuka halaman');
    });

    // Tampilkan loader saat submit form (logout, review, upload, dsb.) + spinner pada tombol.
    document.addEventListener('submit', function (e) {
        const form = e.target;
        if (!(form instanceof HTMLFormElement) || form.hasAttribute('data-no-loader')) return;

        showPageLoader(form.dataset.loaderText || 'Memproses…', form.dataset.loaderSub || 'Mohon tunggu sebentar');

        // Beri spinner pada tombol submit yang ditekan agar tidak terkesan macet.
        const active = document.activeElement;
        const btn = (active && active.tagName === 'BUTTON' && active.form === form)
            ? active : form.querySelector('button[type="submit"], button:not([type])');
        if (btn && !btn.disabled && !btn.classList.contains('dropdown-item')) {
            btn.dataset.lfOriginalHtml = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status"></span>'
                + (form.dataset.loaderText || 'Memproses…');
        }
    }, true);

    // BFCache: saat kembali dengan tombol back, sembunyikan loader & pulihkan tombol.
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) {
            hidePageLoader();
            document.querySelectorAll('[data-lf-original-html]').forEach(function (btn) {
                btn.innerHTML = btn.dataset.lfOriginalHtml;
                delete btn.dataset.lfOriginalHtml;
            });
        }
    });

    // Toggle ngintip password (mata terbuka / tertutup)
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
</script>
@stack('scripts')
</body>
</html>

@extends('layouts.app')

@section('title', 'Pengaturan')

@php($isAdmin = $user->isAdmin())

@section('content')
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-3">
        <div>
            <h4 class="mb-0 fw-bold" style="color:var(--lf-navy)"><i class="bi bi-gear me-2"></i>Pengaturan</h4>
            <div class="text-muted small">Kelola profil, keamanan akun, identitas perusahaan &amp; notifikasi email.</div>
        </div>
        <nav aria-label="breadcrumb" class="d-none d-md-block">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active" aria-current="page">Pengaturan</li>
            </ol>
        </nav>
    </div>

    <div class="card lf-card">
        <div class="card-header bg-white py-3">
            <ul class="nav nav-pills gap-1 lf-settings-nav flex-nowrap" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#tab-profil" type="button" role="tab">
                        <i class="bi bi-person me-1"></i><span class="d-sm-none">Profil</span><span class="d-none d-sm-inline">Profil Saya</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-keamanan" type="button" role="tab">
                        <i class="bi bi-shield-lock me-1"></i><span class="d-sm-none">Password</span><span class="d-none d-sm-inline">Ubah Password</span>
                    </button>
                </li>
                @if ($isAdmin)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-perusahaan" type="button" role="tab">
                            <i class="bi bi-building me-1"></i><span class="d-sm-none">Perusahaan</span><span class="d-none d-sm-inline">Identitas Perusahaan</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" data-bs-toggle="pill" data-bs-target="#tab-smtp" type="button" role="tab">
                            <i class="bi bi-envelope-gear me-1"></i><span class="d-sm-none">SMTP</span><span class="d-none d-sm-inline">Notifikasi Email (SMTP)</span>
                        </button>
                    </li>
                @endif
            </ul>
        </div>

        <div class="card-body p-3 p-md-4">
            <div class="tab-content">

                {{-- ============================ PROFIL ============================ --}}
                <div class="tab-pane fade show active" id="tab-profil" role="tabpanel">
                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="border rounded-3 p-3 p-md-4 h-100">
                                <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-person-badge me-2"></i>Data Profil</h6>
                                <form method="POST" action="{{ route('settings.profile.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label for="name" class="form-label fw-semibold small">Nama Lengkap</label>
                                            <input type="text" id="name" name="name"
                                                   class="form-control @error('name') is-invalid @enderror"
                                                   value="{{ old('name', $user->name) }}" required>
                                            @error('name')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label for="email" class="form-label fw-semibold small">Email</label>
                                            <input type="email" id="email" name="email"
                                                   class="form-control @error('email') is-invalid @enderror"
                                                   value="{{ old('email', $user->email) }}" required>
                                            @error('email')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">
                                        <i class="bi bi-check2-circle me-1"></i>Simpan Profil
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="col-lg-5">
                            <div class="border rounded-3 p-3 p-md-4 h-100 bg-light-subtle">
                                <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-info-circle me-2"></i>Info Akun</h6>
                                <dl class="row row-cols-sm-2 row-cols-1 g-3 mb-0 small">
                                    <div class="col">
                                        <dt class="text-muted fw-normal">Peran</dt>
                                        <dd class="fw-semibold mb-0">{{ $user->role?->label ?? '-' }}</dd>
                                    </div>
                                    <div class="col">
                                        <dt class="text-muted fw-normal">Divisi</dt>
                                        <dd class="fw-semibold mb-0">{{ $user->department?->name ?? 'Tanpa Divisi' }}</dd>
                                    </div>
                                    <div class="col">
                                        <dt class="text-muted fw-normal">Status</dt>
                                        <dd class="mb-0">
                                            <span class="badge {{ $user->isActive() ? 'text-bg-success' : 'text-bg-secondary' }}">
                                                {{ $user->isActive() ? 'Aktif' : 'Nonaktif' }}
                                            </span>
                                        </dd>
                                    </div>
                                    <div class="col">
                                        <dt class="text-muted fw-normal">Bergabung</dt>
                                        <dd class="fw-semibold mb-0">{{ $user->created_at?->translatedFormat('d M Y') ?? '-' }}</dd>
                                    </div>
                                </dl>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ============================ KEAMANAN ============================ --}}
                <div class="tab-pane fade" id="tab-keamanan" role="tabpanel">
                    <div class="row justify-content-center">
                        <div class="col-lg-7 col-xl-6">
                            <div class="border rounded-3 p-3 p-md-4">
                                <div class="d-flex align-items-center gap-2 mb-1" style="color:var(--lf-navy)">
                                    <i class="bi bi-key fs-4"></i>
                                    <h6 class="fw-bold mb-0">Ubah Password</h6>
                                </div>
                                <p class="text-muted small mb-3">Password digunakan untuk masuk ke aplikasi. Gunakan kombinasi yang mudah Anda ingat namun sulit ditebak.</p>
                                <form method="POST" action="{{ route('settings.password.update') }}">
                                    @csrf
                                    @method('PUT')
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label fw-semibold small">Password Saat Ini</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bi bi-lock"></i></span>
                                            <input type="password" id="current_password" name="current_password"
                                                   class="form-control @error('current_password') is-invalid @enderror" required>
                                            <button class="btn btn-outline-secondary bg-white toggle-password" type="button" data-target="#current_password" tabindex="-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('current_password')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="password" class="form-label fw-semibold small">Password Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" id="password" name="password"
                                                   class="form-control @error('password') is-invalid @enderror" minlength="6" required>
                                            <button class="btn btn-outline-secondary bg-white toggle-password" type="button" data-target="#password" tabindex="-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                        @error('password')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="mb-4">
                                        <label for="password_confirmation" class="form-label fw-semibold small">Konfirmasi Password Baru</label>
                                        <div class="input-group">
                                            <span class="input-group-text bg-white"><i class="bi bi-lock-fill"></i></span>
                                            <input type="password" id="password_confirmation" name="password_confirmation" class="form-control" required>
                                            <button class="btn btn-outline-secondary bg-white toggle-password" type="button" data-target="#password_confirmation" tabindex="-1">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 w-sm-auto">
                                        <i class="bi bi-shield-check me-1"></i>Simpan Password Baru
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                @if ($isAdmin)
                    {{-- ============================ PERUSAHAAN ============================ --}}
                    <div class="tab-pane fade" id="tab-perusahaan" role="tabpanel">
                        <div class="row g-4">
                            <div class="col-lg-7">
                                <div class="border rounded-3 p-3 p-md-4 h-100">
                                    <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-building-gear me-2"></i>Identitas Perusahaan</h6>
                                    <form method="POST" action="{{ route('settings.company.update') }}"
                                          enctype="multipart/form-data" class="d-flex flex-wrap gap-4 align-items-center">
                                        @csrf
                                        <div class="text-center">
                                            <img src="{{ company_logo_url() }}" alt="Logo {{ company_name() }}"
                                                 class="border rounded-3 shadow-sm bg-white"
                                                 style="width:104px;height:104px;object-fit:cover;">
                                            <div class="text-muted mt-1" style="font-size:.7rem">Logo saat ini</div>
                                        </div>
                                        <div class="flex-grow-1" style="min-width:240px">
                                            <label for="company_name" class="form-label fw-semibold small">Nama Perusahaan / Aplikasi</label>
                                            <input type="text" id="company_name" name="company_name"
                                                   class="form-control @error('company_name') is-invalid @enderror"
                                                   value="{{ old('company_name', company_name()) }}" required>
                                            @error('company_name')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror

                                            <label for="logo" class="form-label fw-semibold small mt-3">Ganti Logo <span class="text-muted fw-normal">(JPG/PNG/WebP, maks 2 MB)</span></label>
                                            <input type="file" id="logo" name="logo" accept=".jpg,.jpeg,.png,.webp"
                                                   class="form-control @error('logo') is-invalid @enderror">
                                            @error('logo')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            <div class="form-text">Logo tampil di sidebar, halaman login, dan landing page.</div>
                                        </div>
                                        <div class="w-100">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-check2-circle me-1"></i>Simpan Identitas
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-5">
                                <div class="border rounded-3 p-3 p-md-4 h-100 bg-light-subtle">
                                    <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-lightbulb me-2"></i>Tips</h6>
                                    <ul class="small text-muted mb-0 ps-3">
                                        <li class="mb-2">Gunakan logo berbentuk <strong>persegi</strong> agar tampil rapi di sidebar &amp; login.</li>
                                        <li class="mb-2">Nama perusahaan dipakai sebagai nama aplikasi &amp; nama pengirim email notifikasi.</li>
                                        <li>Ukuran disarankan minimal <strong>256×256 px</strong>.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============================ SMTP ============================ --}}
                    @php($smtpEnabled = settings('smtp_enabled') === '1')
                    @php($emailNotifOn = settings('notifications_email_enabled', '1') === '1')
                    <div class="tab-pane fade" id="tab-smtp" role="tabpanel">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
                            <span class="badge {{ $smtpEnabled ? 'text-bg-success' : 'text-bg-secondary' }} p-2">
                                <i class="bi {{ $smtpEnabled ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                {{ $smtpEnabled ? 'SMTP Aktif' : 'SMTP Nonaktif' }}
                            </span>
                            <span class="badge {{ $emailNotifOn && $smtpEnabled ? 'text-bg-success' : 'text-bg-secondary' }} p-2">
                                <i class="bi {{ $emailNotifOn && $smtpEnabled ? 'bi-check-circle' : 'bi-x-circle' }} me-1"></i>
                                Notifikasi Email {{ $emailNotifOn && $smtpEnabled ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </div>

                        <form method="POST" action="{{ route('settings.smtp.update') }}" id="smtp-form">
                            @csrf
                            <div class="row g-4">
                                <div class="col-lg-8">
                                    <div class="border rounded-3 p-3 p-md-4">
                                        <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-server me-2"></i>Konfigurasi SMTP</h6>
                                        <div class="row g-3">
                                            <div class="col-12">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="smtp_enabled" name="smtp_enabled" value="1"
                                                           {{ old('smtp_enabled', $smtpEnabled) ? 'checked' : '' }}>
                                                    <label class="form-check-label fw-semibold small" for="smtp_enabled">
                                                        Aktifkan pengiriman email via SMTP
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="smtp_host" class="form-label fw-semibold small">SMTP Host</label>
                                                <input type="text" id="smtp_host" name="smtp_host" placeholder="smtp.gmail.com"
                                                       class="form-control @error('smtp_host') is-invalid @enderror"
                                                       value="{{ old('smtp_host', settings('smtp_host')) }}" required>
                                                @error('smtp_host')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="smtp_port" class="form-label fw-semibold small">Port</label>
                                                <input type="number" id="smtp_port" name="smtp_port" placeholder="587"
                                                       class="form-control @error('smtp_port') is-invalid @enderror"
                                                       value="{{ old('smtp_port', settings('smtp_port', '587')) }}" required>
                                                @error('smtp_port')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-4">
                                                <label for="smtp_encryption" class="form-label fw-semibold small">Enkripsi</label>
                                                <select id="smtp_encryption" name="smtp_encryption" class="form-select">
                                                    <option value="tls" {{ old('smtp_encryption', settings('smtp_encryption', 'tls')) === 'tls' ? 'selected' : '' }}>TLS (587)</option>
                                                    <option value="ssl" {{ old('smtp_encryption', settings('smtp_encryption')) === 'ssl' ? 'selected' : '' }}>SSL (465)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-8">
                                                <label for="smtp_username" class="form-label fw-semibold small">Username SMTP</label>
                                                <input type="text" id="smtp_username" name="smtp_username" placeholder="email@perusahaan.com"
                                                       class="form-control @error('smtp_username') is-invalid @enderror"
                                                       value="{{ old('smtp_username', settings('smtp_username')) }}">
                                                @error('smtp_username')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="smtp_password" class="form-label fw-semibold small">Password SMTP</label>
                                                <div class="input-group">
                                                    <input type="password" id="smtp_password" name="smtp_password" placeholder="{{ settings('smtp_password') ? '•••••••• (tersimpan)' : '' }}"
                                                           class="form-control @error('smtp_password') is-invalid @enderror">
                                                    <button class="btn btn-outline-secondary bg-white toggle-password" type="button" data-target="#smtp_password" tabindex="-1">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                </div>
                                                <div class="form-text">Kosongkan bila tidak ingin mengubah password.</div>
                                                @error('smtp_password')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-semibold small d-block">Notifikasi Email</label>
                                                <div class="form-check form-switch mt-1">
                                                    <input class="form-check-input" type="checkbox" role="switch" id="notifications_email_enabled" name="notifications_email_enabled" value="1"
                                                           {{ old('notifications_email_enabled', $emailNotifOn) ? 'checked' : '' }}>
                                                    <label class="form-check-label small" for="notifications_email_enabled">
                                                        Kirim notifikasi pengajuan ke email user
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mail_from_address" class="form-label fw-semibold small">Alamat Pengirim (From)</label>
                                                <input type="email" id="mail_from_address" name="mail_from_address" placeholder="no-reply@perusahaan.com"
                                                       class="form-control @error('mail_from_address') is-invalid @enderror"
                                                       value="{{ old('mail_from_address', settings('mail_from_address')) }}" required>
                                                @error('mail_from_address')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                            <div class="col-md-6">
                                                <label for="mail_from_name" class="form-label fw-semibold small">Nama Pengirim</label>
                                                <input type="text" id="mail_from_name" name="mail_from_name"
                                                       class="form-control @error('mail_from_name') is-invalid @enderror"
                                                       value="{{ old('mail_from_name', settings('mail_from_name', company_name())) }}" required>
                                                @error('mail_from_name')<div class="invalid-feedback d-block small">{{ $message }}</div>@enderror
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary mt-4">
                                            <i class="bi bi-check2-circle me-1"></i>Simpan Konfigurasi SMTP
                                        </button>
                                    </div>
                                </div>

                                <div class="col-lg-4">
                                    <div class="border rounded-3 p-3 p-md-4 mb-4">
                                        <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-send me-2"></i>Email Percobaan</h6>
                                        <p class="text-muted small">Tes koneksi SMTP memakai konfigurasi yang terisi di form ini (bisa sebelum disimpan).</p>
                                        <input type="email" name="to" form="smtp-form"
                                               class="form-control @error('to') is-invalid @enderror mb-2"
                                               placeholder="tujuan@email.com" value="{{ old('to') }}" required>
                                        @error('to')<div class="invalid-feedback d-block small mb-2">{{ $message }}</div>@enderror
                                        <button type="button" id="smtp-test-btn" class="btn btn-outline-primary w-100">
                                            <i class="bi bi-paper-plane me-1"></i>Kirim Email Percobaan
                                        </button>
                                    </div>
                                    <div class="border rounded-3 p-3 p-md-4 bg-light-subtle">
                                        <h6 class="fw-bold mb-3" style="color:var(--lf-navy)"><i class="bi bi-lightbulb me-2"></i>Catatan</h6>
                                        <ul class="small text-muted mb-0 ps-3">
                                            <li class="mb-2">Gmail: aktifkan 2FA lalu buat <strong>App Password</strong> di myaccount.google.com.</li>
                                            <li class="mb-2">Port <strong>587</strong> → TLS, port <strong>465</strong> → SSL.</li>
                                            <li>Email dikirim untuk status pengajuan: diajukan, direview, revisi, disetujui &amp; ditolak.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Tombol "Kirim Email Percobaan": alihkan action form SMTP ke endpoint tes,
        // lalu submit (form.submit() melewati validasi required field lain).
        document.getElementById('smtp-test-btn')?.addEventListener('click', function () {
            const form = document.getElementById('smtp-form');
            const to = form.querySelector('input[name="to"]');
            if (!to.value) { to.reportValidity(); return; }
            form.setAttribute('action', '{{ route('settings.smtp.test') }}');
            form.submit();
        });
    </script>
@endpush

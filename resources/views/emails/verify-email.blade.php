{{--
    Email Verifikasi Email — LegalFlow.
    Dipakai oleh App\Notifications\VerifyEmailNotification.
    Variabel: $user (App\Models\User), $verificationUrl (string, signed URL).
    Memakai layout branded (emails.layout) agar konsisten dengan email lain.
--}}
@extends('emails.layout')

@section('emailTitle', 'Verifikasi Email — '.company_name())

@section('emailContent')
    @php($accent = '#2d5da8')

    {{-- Sapaan --}}
    <p style="margin:0 0 18px;font-size:15px;color:#1f2937;line-height:1.7;">
        Halo <strong>{{ $user->name }}</strong>,
    </p>
    <p style="margin:0 0 22px;font-size:14px;color:#4b5563;line-height:1.7;">
        Terima kasih telah mendaftarkan akun Anda di
        <strong>{{ company_name() }}</strong>. Aktifkan akun Anda dengan
        memverifikasi alamat email berikut:
    </p>

    {{-- Banner alamat email yang diverifikasi --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td align="center" style="background-color:#f8fafc;border:1px solid #e8edf5;border-radius:12px;padding:22px 18px;">
                <div style="width:64px;height:64px;margin:0 auto 14px;border-radius:16px;background-color:#e8eef9;text-align:center;">
                    <span style="font-size:30px;line-height:64px;">&#9993;&#65039;</span>
                </div>
                <div style="font-size:11px;font-weight:bold;letter-spacing:1.4px;color:#8a94a6;text-transform:uppercase;">
                    Email Terdaftar
                </div>
                <div style="font-size:17px;font-weight:bold;color:#111827;margin-top:6px;word-break:break-all;">
                    {{ $user->email }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Tombol verifikasi --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 16px;">
        <tr>
            <td align="center">
                <a href="{{ $verificationUrl }}"
                   style="display:inline-block;background-color:{{ $accent }};background:linear-gradient(135deg,#16294f 0%,{{ $accent }} 100%);color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;padding:14px 44px;border-radius:9px;box-shadow:0 4px 12px rgba(45,93,168,.25);">
                    Verifikasi Email Saya
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding-top:14px;font-size:12px;color:#8a94a6;line-height:1.7;">
                Tombol tidak berfungsi? Salin tautan berikut ke browser Anda:<br>
                <a href="{{ $verificationUrl }}" style="color:{{ $accent }};word-break:break-all;">{{ $verificationUrl }}</a>
            </td>
        </tr>
    </table>

    {{-- Catatan masa berlaku & keamanan --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 4px;">
        <tr>
            <td style="background-color:#fdf3dd;border:1px dashed #ecd9a8;border-left:4px solid #d97706;border-radius:10px;padding:13px 18px;font-size:12.5px;color:#92400e;line-height:1.7;">
                <strong>&#9203; Tautan ini berlaku {{ config('verification.expire', 30) }} menit</strong>
                sejak email dikirim. Bila kedaluwarsa, minta tautan baru melalui halaman aplikasi.
            </td>
        </tr>
    </table>

    <p style="margin:18px 0 0;font-size:12.5px;color:#6b7280;line-height:1.7;">
        Jika Anda tidak merasa mendaftarkan akun di {{ company_name() }},
        abaikan email ini — tidak ada tindakan lebih lanjut yang diperlukan.
    </p>
@endsection

@section('emailFootnote')
    Email ini dikirim otomatis karena ada pendaftaran akun baru di {{ company_name() }}
    memakai alamat email ini.<br>
    Mohon tidak membalas email ini secara langsung.
@endsection

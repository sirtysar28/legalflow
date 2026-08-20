{{--
    Email percobaan SMTP (SettingsController::sendTestEmail).
    Variabel: $host, $port, $encryption, $fromName, $fromAddress, $to.
--}}
@extends('emails.layout')

@section('emailTitle', 'Email Percobaan — '.company_name())

@section('emailContent')
    <p style="margin:0 0 18px;font-size:15px;color:#1f2937;line-height:1.7;">
        Halo <strong>Admin</strong> 👋
    </p>
    <p style="margin:0 0 20px;font-size:14px;color:#4b5563;line-height:1.8;">
        Ini adalah <strong>email percobaan</strong> dari aplikasi <strong>{{ company_name() }}</strong>.
        Jika Anda menerima email ini, konfigurasi SMTP sudah benar dan
        notifikasi email aplikasi siap digunakan. ✅
    </p>

    {{-- Detail koneksi SMTP --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;">
        <tr>
            <td style="background-color:#f8fafc;border:1px solid #e8edf5;border-radius:10px;padding:6px 18px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size:13px;color:#374151;">
                    <tr>
                        <td style="padding:9px 0;color:#8a94a6;width:130px;">SMTP Host</td>
                        <td style="padding:9px 0;font-weight:bold;">{{ $host }}</td>
                    </tr>
                    <tr>
                        <td style="padding:9px 0;color:#8a94a6;border-top:1px solid #eef1f7;">Port</td>
                        <td style="padding:9px 0;font-weight:bold;border-top:1px solid #eef1f7;">{{ $port }}</td>
                    </tr>
                    <tr>
                        <td style="padding:9px 0;color:#8a94a6;border-top:1px solid #eef1f7;">Enkripsi</td>
                        <td style="padding:9px 0;font-weight:bold;border-top:1px solid #eef1f7;">{{ strtoupper($encryption ?: 'tanpa enkripsi') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:9px 0;color:#8a94a6;border-top:1px solid #eef1f7;">Pengirim</td>
                        <td style="padding:9px 0;font-weight:bold;border-top:1px solid #eef1f7;">{{ $fromName }} &lt;{{ $fromAddress }}&gt;</td>
                    </tr>
                    <tr>
                        <td style="padding:9px 0;color:#8a94a6;border-top:1px solid #eef1f7;">Tujuan</td>
                        <td style="padding:9px 0;font-weight:bold;border-top:1px solid #eef1f7;">{{ $to }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="font-size:12px;color:#8a94a6;line-height:1.7;">
                Simpan konfigurasi melalui menu
                <strong>Pengaturan &rarr; SMTP &amp; Email</strong> agar notifikasi
                dikirim memakai koneksi ini.
            </td>
        </tr>
    </table>
@endsection

{{--
    Layout dasar email HTML — LegalFlow.
    Semua style inline (bukan class) agar kompatibel dengan mayoritas klien email
    (Gmail, Outlook, Thunderbird, dll). Table-based sesuai praktik terbaik email.
    Variabel opsional: $companyName, $logoUrl, $accentColor (default biru brand).
--}}
@php
    $companyName = $companyName ?? company_name();
    $logoUrl = $logoUrl ?? company_logo_url();
    $accentColor = $accentColor ?? '#2d5da8';
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('emailTitle', 'Notifikasi')</title>
</head>
<body style="margin:0;padding:0;background-color:#edf1f8;font-family:Arial,Helvetica,sans-serif;-webkit-font-smoothing:antialiased;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#edf1f8;">
    <tr>
        <td align="center" style="padding:28px 12px;">

            {{-- ====== KARTU UTAMA ====== --}}
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="width:100%;max-width:600px;background-color:#ffffff;border-radius:16px;overflow:hidden;border:1px solid #e4e9f2;">

                {{-- ====== HEADER ====== --}}
                <tr>
                    <td style="background-color:#16294f;background:linear-gradient(135deg,#16294f 0%,{{ $accentColor }} 100%);padding:30px 32px;text-align:center;">
                        <img src="{{ $logoUrl }}" alt="Logo {{ $companyName }}" width="58" height="58"
                             style="display:inline-block;width:58px;height:58px;border-radius:13px;background-color:#ffffff;border:0;">
                        <div style="color:#ffffff;font-size:20px;font-weight:bold;letter-spacing:.5px;margin-top:12px;">
                            {{ $companyName }}
                        </div>
                        <div style="color:#b9c6de;font-size:12px;margin-top:4px;">
                            Platform Pengajuan, Review &amp; Manajemen Dokumen Legal
                        </div>
                    </td>
                </tr>

                {{-- ====== KONTEN ====== --}}
                <tr>
                    <td style="padding:32px 32px 24px;">
                        @yield('emailContent')
                    </td>
                </tr>

                {{-- ====== FOOTER ====== --}}
                <tr>
                    <td style="padding:20px 32px;background-color:#f7f9fd;border-top:1px solid #e6eaf2;text-align:center;color:#8a94a6;font-size:12px;line-height:1.7;">
                        Email ini dikirim otomatis oleh sistem <strong>{{ $companyName }}</strong>.<br>
                        Mohon tidak membalas email ini secara langsung.<br>
                        &copy; {{ date('Y') }} {{ $companyName }} — LegalFlow by PT Trijaya Solution
                    </td>
                </tr>
            </table>

            {{-- Catatan kecil di bawah kartu --}}
            <table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width:100%;max-width:600px;">
                <tr>
                    <td align="center" style="padding:16px 8px 0;color:#a3adc0;font-size:11px;line-height:1.6;">
                        @yield('emailFootnote')
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>
</body>
</html>

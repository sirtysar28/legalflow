{{--
    Email Verifikasi Email — LegalFlow.
    Dipakai oleh App\Notifications\VerifyEmailNotification.
    Variabel: $user (App\Models\User), $verificationUrl (string, signed URL, berlaku 30 menit).
    Semua style inline & table-based agar kompatibel Gmail/Outlook.
--}}
@php($logoUrl = company_logo_url())

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email {{ config('app.name') }}</title>
</head>

<body style="
    margin:0;
    padding:0;
    background-color:#f4f6f8;
    font-family:Arial, Helvetica, sans-serif;
    color:#333333;
">

<table width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background-color:#f4f6f8; margin:0; padding:0;">

    <tr>
        <td align="center">

            {{-- ===== MAIN CONTAINER ===== --}}
            <table width="600" cellpadding="0" cellspacing="0" border="0"
                   style="
                       width:100%;
                       max-width:600px;
                       background-color:#ffffff;
                       margin:20px auto;
                       border:1px solid #e2e5e8;
                   ">

                {{-- ===== LOGO HEADER ===== --}}
                <tr>
                    <td align="center"
                        style="
                            background-color:#ffffff;
                            padding:22px 20px 18px 20px;
                        ">

                        {{-- Pakai logo perusahaan bila sudah diatur Admin;
                             jika belum, tampilkan branding teks LEGALFLOW. --}}
                        @if (\App\Models\Setting::get('company_logo'))
                            <img src="{{ $logoUrl }}" alt="Logo {{ company_name() }}"
                                 width="150"
                                 style="display:inline-block;width:150px;max-width:70%;height:auto;border:0;">
                        @else
                            <div style="
                                font-size:27px;
                                font-weight:bold;
                                letter-spacing:1px;
                                color:#1e5eff;
                            ">
                                LEGAL<span style="color:#263238;">FLOW</span>
                            </div>
                        @endif

                        <div style="
                            font-size:11px;
                            color:#8a8f98;
                            margin-top:4px;
                            letter-spacing:2px;
                        ">
                            LEGAL MANAGEMENT SYSTEM
                        </div>

                    </td>
                </tr>


                {{-- ===== BLUE HERO ===== --}}
                <tr>
                    <td align="center"
                        style="
                            background:linear-gradient(
                                135deg,
                                #155eef 0%,
                                #1683e8 100%
                            );
                            background-color:#1769e8;
                            padding:42px 30px 45px 30px;
                        ">

                        <div style="
                            font-size:18px;
                            line-height:26px;
                            font-weight:bold;
                            color:#ffffff;
                            margin-bottom:10px;
                        ">
                            Halo, {{ $user->name }}!
                        </div>

                        <div style="
                            font-size:18px;
                            line-height:27px;
                            font-weight:bold;
                            color:#ffffff;
                        ">
                            Selamat Datang di {{ config('app.name') }}
                        </div>

                        <div style="
                            max-width:480px;
                            margin:16px auto 0 auto;
                            font-size:14px;
                            line-height:22px;
                            color:#ffffff;
                        ">
                            Terima kasih telah mendaftarkan akun Anda
                            di {{ config('app.name') }}. Kami siap membantu Anda
                            mengelola proses legal secara lebih mudah,
                            cepat, dan terstruktur.
                        </div>


                        {{-- ===== WHITE CARD ===== --}}
                        <table width="100%" cellpadding="0" cellspacing="0"
                               border="0"
                               style="
                                   max-width:500px;
                                   margin:30px auto 0 auto;
                                   background-color:#ffffff;
                                   border-radius:8px;
                               ">

                            <tr>
                                <td align="center"
                                    style="padding:30px 25px 25px 25px;">

                                    <div style="
                                        font-size:14px;
                                        line-height:22px;
                                        color:#424242;
                                    ">
                                        Untuk mengaktifkan akun dan
                                        menerima informasi penting dari
                                        <strong style="color:#1769e8;">
                                            {{ config('app.name') }}
                                        </strong>,
                                        silakan verifikasi alamat email Anda.
                                    </div>


                                    {{-- ===== BUTTON ===== --}}
                                    <table cellpadding="0"
                                           cellspacing="0"
                                           border="0"
                                           style="margin:25px auto 0 auto;">

                                        <tr>
                                            <td align="center"
                                                bgcolor="#1683e8"
                                                style="
                                                    border-radius:4px;
                                                ">

                                                <a href="{{ $verificationUrl }}"
                                                   style="
                                                       display:inline-block;
                                                       padding:14px 32px;
                                                       font-size:14px;
                                                       font-weight:bold;
                                                       color:#ffffff;
                                                       text-decoration:none;
                                                       background-color:#1683e8;
                                                       border-radius:4px;
                                                   ">
                                                    Verifikasi Email
                                                </a>

                                            </td>
                                        </tr>

                                    </table>

                                </td>
                            </tr>


                            {{-- ===== EXPIRATION ===== --}}
                            <tr>
                                <td align="center"
                                    style="
                                        background-color:#f7f7f7;
                                        border-top:1px solid #eeeeee;
                                        padding:15px 20px;
                                        border-radius:0 0 8px 8px;
                                    ">

                                    <div style="
                                        font-size:11px;
                                        line-height:18px;
                                        color:#777777;
                                        font-style:italic;
                                    ">
                                        Tautan verifikasi ini akan
                                        berlaku selama 30 menit.
                                    </div>

                                    <div style="
                                        font-size:10px;
                                        line-height:16px;
                                        color:#a0a6ad;
                                        margin-top:6px;
                                        word-break:break-all;
                                    ">
                                        Tombol tidak berfungsi? Salin tautan ini ke browser Anda:<br>
                                        <a href="{{ $verificationUrl }}" style="color:#1683e8;">{{ $verificationUrl }}</a>
                                    </div>

                                </td>
                            </tr>

                        </table>

                    </td>
                </tr>


                {{-- ===== SECURITY INFORMATION ===== --}}
                <tr>
                    <td align="center"
                        style="
                            padding:25px 30px 10px 30px;
                            background-color:#ffffff;
                        ">

                        <div style="
                            font-size:12px;
                            line-height:20px;
                            color:#666666;
                        ">
                            Jika Anda tidak merasa melakukan pendaftaran
                            akun {{ config('app.name') }}, abaikan email ini.
                        </div>

                    </td>
                </tr>


                {{-- ===== FOOTER ===== --}}
                <tr>
                    <td align="center"
                        style="
                            padding:10px 30px 25px 30px;
                            background-color:#ffffff;
                        ">

                        <div style="
                            font-size:12px;
                            line-height:18px;
                            color:#9aa0a6;
                        ">
                            &copy; {{ date('Y') }} {{ company_name() }}
                        </div>

                        <div style="
                            font-size:11px;
                            line-height:18px;
                            color:#b0b4b8;
                            margin-top:4px;
                        ">
                            Legal Management System
                        </div>

                    </td>
                </tr>

            </table>

        </td>
    </tr>

</table>

</body>
</html>

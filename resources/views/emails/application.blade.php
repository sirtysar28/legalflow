{{--
    Email notifikasi pengajuan (dipakai App\Notifications\ApplicationNotification).
    Variabel: $greeting, $title, $body, $url, $cta, $levelLabel, $color, $bgColor.
--}}
@extends('emails.layout')

@section('emailTitle', $title.' — '.company_name())

@section('emailContent')
    {{-- Sapaan --}}
    <p style="margin:0 0 18px;font-size:15px;color:#1f2937;line-height:1.7;">
        Halo <strong>{{ $greeting }}</strong>,
    </p>
    <p style="margin:0 0 20px;font-size:14px;color:#4b5563;line-height:1.7;">
        Anda menerima notifikasi baru terkait pengajuan dokumen legal:
    </p>

    {{-- Banner judul dengan warna sesuai jenis notifikasi --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 20px;">
        <tr>
            <td style="background-color:{{ $bgColor }};border-left:4px solid {{ $color }};border-radius:10px;padding:16px 18px;">
                <div style="font-size:11px;font-weight:bold;letter-spacing:1.4px;color:{{ $color }};text-transform:uppercase;">
                    {{ $levelLabel }}
                </div>
                <div style="font-size:17px;font-weight:bold;color:#111827;margin-top:5px;">
                    {{ $title }}
                </div>
            </td>
        </tr>
    </table>

    {{-- Isi pesan --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 26px;">
        <tr>
            <td style="background-color:#f8fafc;border:1px solid #e8edf5;border-radius:10px;padding:16px 18px;font-size:14px;color:#374151;line-height:1.8;">
                {{ $body }}
            </td>
        </tr>
    </table>

    {{-- Tombol aksi --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}"
                   style="display:inline-block;background-color:#2d5da8;color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;padding:13px 38px;border-radius:9px;">
                    {{ $cta }} &rarr;
                </a>
            </td>
        </tr>
        <tr>
            <td align="center" style="padding-top:16px;font-size:12px;color:#8a94a6;line-height:1.7;">
                Tombol tidak berfungsi? Salin tautan berikut ke browser Anda:<br>
                <a href="{{ $url }}" style="color:#2d5da8;word-break:break-all;">{{ $url }}</a>
            </td>
        </tr>
    </table>
@endsection

@section('emailFootnote')
    Anda menerima email ini karena terdaftar sebagai pengguna aplikasi {{ company_name() }}.<br>
    Untuk berhenti menerima notifikasi email, hubungi Admin aplikasi.
@endsection

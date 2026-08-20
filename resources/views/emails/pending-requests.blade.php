{{--
    Email digest untuk Admin: daftar pengajuan yang masih DRAFT
    ("belum masuk" ke proses Legal). Dipakai oleh
    App\Notifications\PendingRequestsNotification.
    Variabel: $greeting, $requests (array), $url.
    Semua style inline agar kompatibel Gmail/Outlook.
--}}
@extends('emails.layout')

@php($total = count($requests))
@php($accent = '#d97706') {{-- amber: perlu perhatian, bukan darurat --}}

@section('emailTitle', $total.' Pengajuan Belum Masuk — '.company_name())

@section('emailContent')

    {{-- Sapaan --}}
    <p style="margin:0 0 16px;font-size:15px;color:#1f2937;line-height:1.7;">
        Halo <strong>{{ $greeting }}</strong>,
    </p>

    {{-- Banner ringkasan --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 22px;">
        <tr>
            <td style="background:linear-gradient(135deg,#fff7ed 0%,#fdf3dd 100%);background-color:#fdf3dd;border:1px solid #f5deb3;border-left:4px solid {{ $accent }};border-radius:12px;padding:20px 22px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="vertical-align:middle;">
                            <div style="font-size:11px;font-weight:bold;letter-spacing:1.4px;color:{{ $accent }};text-transform:uppercase;">
                                Pengingat Rutin
                            </div>
                            <div style="font-size:19px;font-weight:bold;color:#111827;margin-top:6px;">
                                @if ($total === 1)
                                    Ada 1 pengajuan yang belum masuk
                                @else
                                    Ada {{ $total }} pengajuan yang belum masuk
                                @endif
                            </div>
                            <div style="font-size:13px;color:#6b7280;margin-top:6px;line-height:1.6;">
                                Pengajuan berikut masih berstatus <strong>Draft</strong> —
                                belum diajukan ke Legal untuk diproses.
                            </div>
                        </td>
                        <td width="86" style="vertical-align:middle;text-align:center;">
                            <div style="width:74px;height:74px;border-radius:18px;background-color:#ffffff;border:1px solid #f5deb3;text-align:center;">
                                <div style="font-size:28px;font-weight:bold;color:{{ $accent }};line-height:74px;">
                                    {{ $total }}
                                </div>
                            </div>
                            <div style="font-size:10px;color:#9ca3af;margin-top:6px;text-transform:uppercase;letter-spacing:1px;">
                                Draft
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- ===== TABEL DAFTAR PENGAJUAN ===== --}}
    <p style="margin:0 0 10px;font-size:13px;font-weight:bold;color:#374151;text-transform:uppercase;letter-spacing:1px;">
        Rincian Pengajuan
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="border:1px solid #e8edf5;border-radius:12px;overflow:hidden;margin:0 0 24px;">
        @foreach ($requests as $request)
            <tr>
                <td style="padding:14px 18px;background-color:{{ $loop->even ? '#fbfcfe' : '#ffffff' }};border-bottom:1px solid #eef2f8;{{ $loop->last ? 'border-bottom:none;' : '' }}">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="vertical-align:top;width:40px;">
                                <div style="width:36px;height:36px;border-radius:10px;background-color:#fdf3dd;text-align:center;">
                                    <span style="font-size:16px;line-height:36px;">&#128221;</span>
                                </div>
                            </td>
                            <td style="vertical-align:top;padding-left:12px;">
                                <div style="font-size:14px;font-weight:bold;color:#111827;line-height:1.4;">
                                    {{ $request['title'] }}
                                </div>
                                <div style="font-size:12px;color:#6b7280;margin-top:3px;line-height:1.6;">
                                    <strong style="color:#2d5da8;">{{ $request['number'] }}</strong>
                                    &nbsp;&bull;&nbsp; {{ $request['type'] }}
                                </div>
                                <div style="font-size:12px;color:#9ca3af;margin-top:3px;line-height:1.6;">
                                    Pemohon: <strong style="color:#4b5563;">{{ $request['owner'] }}</strong>
                                    @if ($request['department'])
                                        &nbsp;&bull;&nbsp; Divisi: {{ $request['department'] }}
                                    @endif
                                    &nbsp;&bull;&nbsp; Draft selama <strong style="color:{{ $accent }};">{{ $request['age'] }}</strong>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endforeach
    </table>

    {{-- Info tindak lanjut --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="background-color:#f8fafc;border:1px dashed #d3dce9;border-radius:10px;padding:14px 18px;font-size:13px;color:#4b5563;line-height:1.8;">
                <strong>Apa yang bisa Anda lakukan?</strong><br>
                Hubungi pemohon untuk menindaklanjuti draft-nya, atau buka daftar
                pengajuan untuk memantau statusnya secara langsung.
            </td>
        </tr>
    </table>

    {{-- Tombol aksi --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <a href="{{ $url }}"
                   style="display:inline-block;background:linear-gradient(135deg,#16294f 0%,#2d5da8 100%);background-color:#2d5da8;color:#ffffff;text-decoration:none;font-weight:bold;font-size:14px;padding:14px 40px;border-radius:10px;box-shadow:0 4px 12px rgba(45,93,168,.25);">
                    Lihat Daftar Pengajuan &rarr;
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
    Email ini adalah pengingat otomatis yang dikirim kepada Admin {{ company_name() }}
    sesuai jadwal pemeriksaan pengajuan Draft.<br>
    Untuk berhenti menerima notifikasi email, hubungi Admin aplikasi.
@endsection

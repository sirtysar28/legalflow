<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(\Illuminate\Foundation\Inspiring::quote());
})->purpose('Display an inspiring quote');

/*
|--------------------------------------------------------------------------
| Pengingat Pengajuan Belum Masuk (DRAFT)
|--------------------------------------------------------------------------
| Setiap hari pukul 08:00, cek pengajuan yang masih Draft lebih dari 24 jam
| dan kirim notifikasi (in-app + email) ke semua Admin.
| Jalankan scheduler di server produksi: * * * * * php artisan schedule:run
*/
Schedule::command('app:notify-pending-requests')->dailyAt('08:00');

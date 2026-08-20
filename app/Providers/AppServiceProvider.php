<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Paginator::useBootstrapFive();

        $this->applyApplicationSettings();
    }

    /**
     * Terapkan pengaturan dari database ke konfigurasi runtime:
     * nama perusahaan + SMTP email (diatur Admin lewat menu Pengaturan).
     */
    private function applyApplicationSettings(): void
    {
        try {
            if (! Schema::hasTable('settings')) {
                return;
            }
        } catch (\Throwable) {
            return; // Database belum siap (mis. saat instalasi awal).
        }

        if ($name = Setting::get('company_name')) {
            config(['app.name' => $name]);
        }

        if (Setting::get('smtp_enabled') === '1') {
            config([
                'mail.default' => 'smtp',
                'mail.mailers.smtp.host' => Setting::get('smtp_host', config('mail.mailers.smtp.host')),
                'mail.mailers.smtp.port' => (int) Setting::get('smtp_port', 587),
                'mail.mailers.smtp.encryption' => Setting::get('smtp_encryption') ?: null,
                'mail.mailers.smtp.username' => Setting::get('smtp_username'),
                'mail.mailers.smtp.password' => Setting::getDecrypted('smtp_password'),
                'mail.from.address' => Setting::get('mail_from_address', config('mail.from.address')),
                'mail.from.name' => Setting::get('mail_from_name', config('mail.from.name')),
            ]);
        }
    }
}

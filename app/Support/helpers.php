<?php

use App\Models\Setting;
use App\Support\WebsiteContent;

if (! function_exists('settings')) {
    /**
     * Ambil nilai pengaturan dari database.
     */
    function settings(string $key, ?string $default = null): ?string
    {
        return Setting::get($key, $default);
    }
}

if (! function_exists('company_name')) {
    function company_name(): string
    {
        return Setting::get('company_name', config('app.name')) ?? 'LegalFlow';
    }
}

if (! function_exists('company_logo_url')) {
    /**
     * URL logo perusahaan: hasil upload admin bila ada,
     * jika tidak pakai logo bawaan.
     */
    function company_logo_url(): string
    {
        $path = Setting::get('company_logo');

        if ($path && is_file(public_path($path))) {
            return asset($path).'?v='.filemtime(public_path($path));
        }

        return asset('images/logo_legalflow.jpg');
    }
}

if (! function_exists('website_content')) {
    /**
     * Ambil konten landing page per section (merge dengan default).
     */
    function website_content(string $section): array
    {
        return WebsiteContent::get($section);
    }
}

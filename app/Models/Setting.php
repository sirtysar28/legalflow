<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

/**
 * Pengaturan aplikasi (key-value) yang tersimpan di database:
 * identitas perusahaan (nama & logo) dan konfigurasi SMTP email.
 */
class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    /** Cache per-request agar query tidak berulang. */
    protected static ?array $cache = null;

    public static function get(string $key, ?string $default = null): ?string
    {
        return self::allSettings()[$key] ?? $default;
    }

    public static function set(string $key, ?string $value): void
    {
        static::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        if (static::$cache !== null) {
            static::$cache[$key] = $value;
        }
    }

    /**
     * Nilai terenkripsi (mis. password SMTP).
     */
    public static function setEncrypted(string $key, ?string $value): void
    {
        self::set($key, filled($value) ? Crypt::encryptString($value) : null);
    }

    public static function getDecrypted(string $key, ?string $default = null): ?string
    {
        $value = self::get($key);

        if (blank($value)) {
            return $default;
        }

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $default;
        }
    }

    public static function forgetCache(): void
    {
        static::$cache = null;
    }

    protected static function allSettings(): array
    {
        if (static::$cache === null) {
            try {
                static::$cache = static::query()->pluck('value', 'key')->all();
            } catch (\Throwable) {
                // Tabel belum ada (sebelum migrasi) — anggap kosong.
                static::$cache = [];
            }
        }

        return static::$cache;
    }
}

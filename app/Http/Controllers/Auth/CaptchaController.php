<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Response;

/**
 * CAPTCHA sederhana berbasis session (tanpa dependency eksternal):
 * - Kode 5 karakter acak (tanpa huruf ambigu: I, O, 0, 1) disimpan
 *   sebagai hash SHA-256 di session dan berlaku 10 menit.
 * - Gambar dirender dengan GD: karakter miring acak + noise garis/titik.
 * - Validasi dilakukan di LoginController (case-insensitive, sekali pakai).
 */
class CaptchaController extends Controller
{
    /** Umur kode captcha dalam menit. */
    public const TTL_MINUTES = 10;

    /** Karakter yang aman dibaca (tanpa I, O, 0, 1 yang mudah tertukar). */
    private const CHARSET = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';

    /**
     * Generate kode baru, simpan hash-nya ke session, dan
     * kembalikan gambar PNG-nya.
     */
    public function show(): Response
    {
        $code = $this->generateCode();

        session([
            'captcha_hash' => hash('sha256', $code),
            'captcha_expires_at' => now()->addMinutes(self::TTL_MINUTES)->timestamp,
        ]);

        return $this->renderImage($code)
            ->header('Content-Type', 'image/png')
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache');
    }

    /**
     * Verifikasi input user terhadap hash di session.
     * Kode bersifat sekali pakai — hash dihapus meskipun valid.
     */
    public static function verify(?string $input): bool
    {
        $hash = session('captcha_hash');
        $expires = session('captcha_expires_at');

        // Selalu hapus: captcha tidak boleh dipakai ulang.
        session()->forget(['captcha_hash', 'captcha_expires_at']);

        if (! $hash || ! $expires || now()->timestamp > (int) $expires) {
            return false;
        }

        return hash_equals($hash, hash('sha256', strtoupper(trim((string) $input))));
    }

    private function generateCode(int $length = 5): string
    {
        $out = '';

        for ($i = 0; $i < $length; $i++) {
            $out .= self::CHARSET[random_int(0, strlen(self::CHARSET) - 1)];
        }

        return $out;
    }

    /**
     * Render kode menjadi gambar captcha (TTF bila tersedia,
     * fallback ke font bawaan GD yang dirotasi per karakter).
     */
    private function renderImage(string $code): Response
    {
        [$width, $height] = [210, 56];
        $image = imagecreatetruecolor($width, $height);

        // Latar lembut kebiruan.
        imagefill($image, 0, 0, imagecolorallocate($image, 243, 246, 252));

        // Titik-titik noise.
        for ($i = 0; $i < 120; $i++) {
            imagesetpixel(
                $image,
                random_int(0, $width - 1),
                random_int(0, $height - 1),
                imagecolorallocate($image, random_int(190, 224), random_int(200, 232), random_int(216, 244))
            );
        }

        // Garis-garis noise melengkung.
        for ($i = 0; $i < 5; $i++) {
            imageline(
                $image,
                random_int(-10, $width),
                random_int(0, $height),
                random_int(-10, $width),
                random_int(0, $height),
                imagecolorallocate($image, random_int(150, 190), random_int(165, 205), random_int(195, 230))
            );
        }

        $font = $this->findFont();
        $palette = [[15, 30, 61], [22, 41, 79], [38, 68, 122], [45, 93, 168], [58, 76, 140]];

        if ($font) {
            $size = 24;
            $x = 22;

            foreach (str_split($code) as $char) {
                [$r, $g, $b] = $palette[random_int(0, count($palette) - 1)];
                imagettftext(
                    $image,
                    $size,
                    random_int(-16, 16),
                    $x,
                    random_int(38, 43),
                    imagecolorallocate($image, $r, $g, $b),
                    $font,
                    $char
                );

                $box = imagettfbbox($size, 0, $font, $char);
                $x += max($box[2] - $box[0], 14) + 8;
            }
        } else {
            // Fallback: gambar tiap karakter di kanvas kecil, rotasi, lalu tempel.
            $x = 14;

            foreach (str_split($code) as $char) {
                [$r, $g, $b] = $palette[random_int(0, count($palette) - 1)];

                $tmp = imagecreatetruecolor(40, 48);
                imagesavealpha($tmp, true);
                $transparent = imagecolorallocatealpha($tmp, 0, 0, 0, 127);
                imagefill($tmp, 0, 0, $transparent);
                imagestring($tmp, 5, 10, 14, $char, imagecolorallocate($tmp, $r, $g, $b));

                $rotated = imagerotate($tmp, random_int(-18, 18), $transparent);
                imagesavealpha($rotated, true);

                imagecopy(
                    $image,
                    $rotated,
                    $x,
                    (int) ((56 - imagesy($rotated)) / 2),
                    0,
                    0,
                    imagesx($rotated),
                    imagesy($rotated)
                );

                $x += 36;
                imagedestroy($tmp);
                imagedestroy($rotated);
            }
        }

        ob_start();
        imagepng($image, null, 6);
        imagedestroy($image);

        return response(ob_get_clean());
    }

    /**
     * Cari font TTF yang tersedia di server (Linux/production, macOS, Windows).
     */
    private function findFont(): ?string
    {
        foreach ([
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf', // Debian/Ubuntu/cPanel
            '/usr/share/fonts/dejavu/DejaVuSans-Bold.ttf',
            '/usr/share/fonts/TTF/DejaVuSans-Bold.ttf',              // Arch
            '/System/Library/Fonts/Supplemental/Arial Bold.ttf',     // macOS
            'C:/Windows/Fonts/arialbd.ttf',                          // Windows
        ] as $font) {
            if (is_readable($font)) {
                return $font;
            }
        }

        return null;
    }
}

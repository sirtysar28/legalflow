<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Konten landing page yang bisa dikelola Super Admin
 * lewat menu "Kelola Website".
 *
 * Setiap section disimpan sebagai JSON di tabel settings
 * (key: web_hero, web_features, dst). Nilai default dipakai
 * bila section/belum pernah diubah.
 */
class WebsiteContent
{
    public const SECTIONS = ['seo', 'hero', 'features', 'workflow', 'stats', 'modules', 'cta', 'footer'];

    /** Key berisi daftar item yang bisa diulang — dipakai apa adanya dari yang tersimpan. */
    private const LIST_KEYS = ['items', 'steps', 'highlights'];

    public static function defaults(): array
    {
        return [
            'seo' => [
                'title' => 'LegalFlow — Platform Pengajuan, Review & Manajemen Dokumen Legal',
                'description' => 'LegalFlow: platform terintegrasi untuk pengurusan perizinan (NIB, PBG, SLF, UKL-UPL, Sertifikasi Halal, TDG), agreement lintas divisi, dan document management dengan kontrol akses.',
            ],

            'hero' => [
                'badge' => 'Platform Legal Management Terintegrasi',
                'title_start' => 'Kelola',
                'title_end' => 'Tanpa Ribet, Semua Terpusat.',
                'typing_words' => 'Perizinan, Agreement, Dokumen Legal',
                'lead' => 'LegalFlow menyatukan **pengurusan perizinan** (NIB, PBG, SLF-UPL, Sertifikasi Halal, TDG), **agreement lintas divisi**, dan **document management** dalam satu platform — lengkap dengan review legal, audit trail, dan kontrol akses antar divisi.',
                'cta_primary' => 'Mulai Sekarang',
                'cta_secondary' => 'Lihat Cara Kerja',
                'highlights' => [
                    'Review berjenjang Legal & Admin',
                    'Audit trail otomatis',
                    'Kontrol akses dokumen',
                ],
                'scroll_hint' => 'Pelajari lebih lanjut',
            ],

            'features' => [
                'eyebrow' => 'Fitur Utama',
                'title' => 'Satu Platform, Semua Kebutuhan Legal',
                'subtitle' => 'Dari pengajuan sampai dokumen terbit — semua terkontrol, tercatat, dan terlacak.',
                'items' => [
                    ['icon' => 'bi bi-file-earmark-text', 'color' => 'blue', 'title' => 'Submission Management', 'points' => "Form multi-step per jenis izin & agreement\nPersyaratan dokumen otomatis menyesuaikan\nSimpan draft / ajukan ke Legal\nProgress bar kelengkapan dokumen"],
                    ['icon' => 'bi bi-clipboard-check', 'color' => 'green', 'title' => 'Review & Approval', 'points' => "Antrean review Legal/Admin terpusat\nDetail 3-tab: Informasi, Dokumen, Riwayat\nSetujui / Minta Revisi / Tolak\nSiklus revisi sampai pengajuan final"],
                    ['icon' => 'bi bi-folder-check', 'color' => 'amber', 'title' => 'Document Management', 'points' => "Folder otomatis per divisi saat terbit\nVersioning dokumen (v1, v2, dst)\nDeteksi kadaluarsa izin & kontrak\nUnduh terotorisasi via controller"],
                    ['icon' => 'bi bi-person-lock', 'color' => 'purple', 'title' => 'Kontrol Akses Lintas Divisi', 'points' => "Minta akses dokumen divisi lain\nJenis akses: View Only / View+Download\nMasa berlaku akses & expired otomatis"],
                    ['icon' => 'bi bi-truck', 'color' => 'blue', 'title' => 'Integrasi Supplier Assessment', 'points' => "Status, Risk Level & Score otomatis\nGuard: hanya supplier lolos assessment\nData langsung tampil saat review"],
                    ['icon' => 'bi bi-clock-history', 'color' => 'green', 'title' => 'Audit Trail & Notifikasi', 'points' => "Setiap aksi tercatat: pelaku, waktu, catatan\nNotifikasi in-app real-time\nDashboard statistik per role"],
                ],
            ],

            'workflow' => [
                'eyebrow' => 'Cara Kerja',
                'title' => 'Alur End-to-End yang Jelas',
                'steps' => [
                    ['icon' => 'bi bi-pencil-square', 'accent' => 'blue',   'title' => '1. Buat Pengajuan',  'desc' => 'Form + upload dokumen persyaratan'],
                    ['icon' => 'bi bi-send',          'accent' => 'blue',   'title' => '2. Ajukan ke Legal', 'desc' => 'Atau simpan sebagai draft'],
                    ['icon' => 'bi bi-search',        'accent' => 'blue',   'title' => '3. Review Legal',   'desc' => 'Cek informasi, dokumen, riwayat'],
                    ['icon' => 'bi bi-arrow-repeat',  'accent' => 'blue',   'title' => '4. Revisi (Opsional)', 'desc' => 'Lengkapi dokumen, ajukan ulang'],
                    ['icon' => 'bi bi-patch-check',   'accent' => 'green',  'title' => '5. Terbit / Disetujui', 'desc' => 'Dokumen tersimpan otomatis'],
                    ['icon' => 'bi bi-key',           'accent' => 'amber',  'title' => '6. Kontrol Akses',   'desc' => 'Divisi lain minta akses dokumen'],
                ],
            ],

            'stats' => [
                'items' => [
                    ['value' => '3',   'suffix' => '',   'label' => 'Modul Terintegrasi'],
                    ['value' => '8',   'suffix' => '+',  'label' => 'Jenis Izin Didukung'],
                    ['value' => '100', 'suffix' => '%',  'label' => 'Aksi Tercatat Audit Trail'],
                    ['value' => '24',  'suffix' => '/7', 'label' => 'Monitoring Pengajuan'],
                ],
            ],

            'modules' => [
                'eyebrow' => 'Modul',
                'title' => 'Tiga Modul, Satu Ekosistem',
                'items' => [
                    ['icon' => 'bi bi-buildings', 'color' => 'blue',  'title' => 'Modul Perizinan', 'desc' => 'Pengurusan izin usaha & operasional: NIB, PBG, SLF, UKL-UPL, Sertifikasi Halal, TDG — dikelompokkan per kategori (Usaha, Bangunan, Lingkungan, Produk, Operasional).', 'tags' => 'NIB, PBG, SLF, UKL-UPL, Halal, TDG'],
                    ['icon' => 'bi bi-handshake', 'color' => 'green', 'title' => 'Modul Agreement', 'desc' => 'Pengajuan perjanjian/kontrak lintas divisi — Purchasing, HR, IT, Finance, Marketing, Operasional. Terhubung langsung ke Supplier Assessment System.', 'tags' => 'Purchasing, HR, Finance, IT'],
                    ['icon' => 'bi bi-archive',   'color' => 'amber', 'title' => 'Modul Document Management', 'desc' => 'Penyimpanan terpusat dokumen hasil perizinan & agreement — folder per divisi, sub-folder manual, kontrol akses lintas divisi dengan masa berlaku.', 'tags' => 'Folder Divisi, Versioning, Access Request'],
                ],
            ],

            'cta' => [
                'title' => 'Siap Rapikan Urusan Legal Perusahaan?',
                'text' => 'Coba demo LegalFlow sekarang — tersedia akun Admin, Legal, User, dan Purchasing untuk merasakan alur lengkapnya.',
                'button' => 'Masuk & Coba Demo',
            ],

            'footer' => [
                'tagline' => 'Platform Pengajuan, Review & Manajemen Dokumen Legal',
                'copyright' => '© :year PT Trijaya Solution',
            ],
        ];
    }

    /**
     * Ambil satu section (merge dengan default agar aman).
     */
    public static function get(string $section): array
    {
        $defaults = self::defaults()[$section] ?? [];

        $raw = Setting::get('web_'.$section);

        if (blank($raw)) {
            return $defaults;
        }

        try {
            $saved = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable) {
            return $defaults;
        }

        if (! is_array($saved)) {
            return $defaults;
        }

        return self::merge($defaults, $saved);
    }

    /**
     * Gabungkan konten tersimpan dengan default. Untuk daftar item
     * (items/steps/highlights) dipakai seluruhnya dari yang tersimpan
     * agar item yang dihapus Super Admin tidak "muncul lagi" dari default.
     */
    private static function merge(array $defaults, array $saved): array
    {
        foreach ($defaults as $key => $default) {
            if (! array_key_exists($key, $saved)) {
                continue; // tidak diubah → pakai default
            }

            $value = $saved[$key];

            if (in_array($key, self::LIST_KEYS, true)) {
                $defaults[$key] = is_array($value) ? array_values($value) : [];

                continue;
            }

            if (is_array($default) && is_array($value)) {
                $defaults[$key] = self::merge($default, $value);

                continue;
            }

            $defaults[$key] = $value;
        }

        return $defaults;
    }

    public static function all(): array
    {
        $result = [];

        foreach (self::SECTIONS as $section) {
            $result[$section] = self::get($section);
        }

        return $result;
    }

    public static function set(string $section, array $data): void
    {
        Setting::set('web_'.$section, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    public static function reset(string $section): void
    {
        Setting::set('web_'.$section, null);
    }

    /**
     * URL gambar hero (opsional, hasil upload Super Admin).
     */
    public static function heroImageUrl(): ?string
    {
        $path = Setting::get('web_hero_image');

        if ($path && is_file(public_path($path))) {
            return asset($path).'?v='.filemtime(public_path($path));
        }

        return null;
    }
}

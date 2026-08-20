<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    /**
     * Halaman Pengaturan: profil, ubah password, identitas perusahaan & SMTP.
     */
    public function index(): Response
    {
        return response()->view('settings.index', [
            'user' => auth()->user(),
        ]);
    }

    /**
     * Perbarui profil (nama & email) milik user yang sedang login.
     */
    public function updateProfile(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($user->id)],
        ], [
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan user lain.',
        ]);

        $user->update($validated);

        return redirect()
            ->to('/settings#tab-profil')
            ->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ubah password milik user yang sedang login.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'current_password:web'],
            'password' => ['required', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Password saat ini wajib diisi.',
            'current_password.current_password' => 'Password saat ini salah.',
            'password.required' => 'Password baru wajib diisi.',
            'password.min' => 'Password baru minimal 6 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user->update(['password' => $validated['password']]);

        return redirect()
            ->to('/settings#tab-keamanan')
            ->with('success', 'Password berhasil diubah.');
    }

    /**
     * [Admin] Perbarui identitas perusahaan: nama & logo.
     */
    public function updateCompany(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:100'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'company_name.required' => 'Nama perusahaan wajib diisi.',
            'logo.image' => 'File harus berupa gambar.',
            'logo.mimes' => 'Logo harus berformat JPG, PNG, atau WebP.',
            'logo.max' => 'Ukuran logo maksimal 2 MB.',
        ]);

        Setting::set('company_name', $validated['company_name']);

        if ($request->hasFile('logo')) {
            $this->storeLogo($request);
        }

        return redirect()
            ->to('/settings#tab-perusahaan')
            ->with('success', 'Identitas perusahaan berhasil diperbarui.');
    }

    /**
     * Simpan file logo ke public/uploads dan hapus logo lama.
     */
    private function storeLogo(Request $request): void
    {
        $old = Setting::get('company_logo');

        if ($old && is_file(public_path($old))) {
            @unlink(public_path($old));
        }

        $file = $request->file('logo');
        $name = 'logo-'.now()->format('Ymd-His').'.'.$file->getClientOriginalExtension();

        $file->move(public_path('uploads'), $name);

        Setting::set('company_logo', 'uploads/'.$name);
    }

    /**
     * [Admin] Simpan konfigurasi SMTP untuk notifikasi email.
     */
    public function updateSmtp(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'smtp_enabled' => ['nullable', 'boolean'],
            'smtp_host' => ['required', 'string', 'max:190'],
            'smtp_port' => ['required', 'integer', 'between:1,65535'],
            'smtp_encryption' => ['nullable', 'in:tls,ssl'],
            'smtp_username' => ['nullable', 'string', 'max:190'],
            'smtp_password' => ['nullable', 'string', 'max:500'],
            'mail_from_address' => ['required', 'email', 'max:190'],
            'mail_from_name' => ['required', 'string', 'max:100'],
            'notifications_email_enabled' => ['nullable', 'boolean'],
        ], [
            'smtp_host.required' => 'SMTP Host wajib diisi.',
            'smtp_port.required' => 'SMTP Port wajib diisi.',
            'smtp_port.integer' => 'SMTP Port harus berupa angka.',
            'smtp_port.between' => 'SMTP Port harus di antara 1–65535.',
            'smtp_encryption.in' => 'Enkripsi hanya boleh TLS atau SSL.',
            'mail_from_address.required' => 'Alamat pengirim wajib diisi.',
            'mail_from_address.email' => 'Format alamat pengirim tidak valid.',
            'mail_from_name.required' => 'Nama pengirim wajib diisi.',
        ]);

        Setting::set('smtp_enabled', ($validated['smtp_enabled'] ?? false) ? '1' : '0');
        Setting::set('smtp_host', $validated['smtp_host']);
        Setting::set('smtp_port', (string) $validated['smtp_port']);
        Setting::set('smtp_encryption', $validated['smtp_encryption'] ?? 'tls');
        Setting::set('smtp_username', $validated['smtp_username'] ?? null);

        // Password dikosongkan = pertahankan yang lama.
        if (filled($validated['smtp_password'] ?? null)) {
            Setting::setEncrypted('smtp_password', $validated['smtp_password']);
        }

        Setting::set('mail_from_address', $validated['mail_from_address']);
        Setting::set('mail_from_name', $validated['mail_from_name']);
        Setting::set('notifications_email_enabled', ($validated['notifications_email_enabled'] ?? false) ? '1' : '0');

        Setting::forgetCache();

        return redirect()
            ->to('/settings#tab-smtp')
            ->with('success', 'Konfigurasi SMTP berhasil disimpan.');
    }

    /**
     * [Admin] Kirim email percobaan memakai konfigurasi dari form
     * (bisa dites sebelum disimpan).
     */
    public function sendTestEmail(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'to' => ['required', 'email'],
        ], [
            'to.required' => 'Alamat email tujuan wajib diisi.',
            'to.email' => 'Format email tujuan tidak valid.',
        ]);

        // Prioritas nilai dari form, fallback ke yang tersimpan.
        $host = $request->input('smtp_host') ?: Setting::get('smtp_host');
        $port = (int) ($request->input('smtp_port') ?: Setting::get('smtp_port', 587));
        $username = $request->input('smtp_username') ?: Setting::get('smtp_username');
        $password = filled($request->input('smtp_password'))
            ? $request->input('smtp_password')
            : Setting::getDecrypted('smtp_password');
        $encryption = $request->input('smtp_encryption') ?: Setting::get('smtp_encryption', 'tls');
        $fromAddress = $request->input('mail_from_address') ?: Setting::get('mail_from_address', 'legalflow@localhost');
        $fromName = $request->input('mail_from_name') ?: Setting::get('mail_from_name', company_name());

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $host,
            'mail.mailers.smtp.port' => $port,
            'mail.mailers.smtp.encryption' => $encryption ?: null,
            'mail.mailers.smtp.username' => $username,
            'mail.mailers.smtp.password' => $password,
            'mail.from.address' => $fromAddress,
            'mail.from.name' => $fromName,
        ]);

        try {
            Mail::send(
                'emails.test',
                [
                    'host'       => $host,
                    'port'       => $port,
                    'encryption' => $encryption,
                    'fromName'   => $fromName,
                    'fromAddress'=> $fromAddress,
                    'to'         => $validated['to'],
                ],
                function ($message) use ($validated, $fromName) {
                    $message->to($validated['to'])
                        ->subject("Email Percobaan — {$fromName}");
                }
            );
        } catch (\Throwable $e) {
            return redirect()
                ->to('/settings#tab-smtp')
                ->withInput($request->except(['smtp_password']))
                ->with('danger', 'Gagal mengirim email: '.$e->getMessage());
        }

        return redirect()
            ->to('/settings#tab-smtp')
            ->withInput($request->except(['smtp_password']))
            ->with('success', "Email percobaan berhasil dikirim ke {$validated['to']}.");
    }
}

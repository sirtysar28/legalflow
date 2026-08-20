<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;

/**
 * Email verifikasi dengan template custom (emails.verify-email):
 * hero gradasi biru, kartu putih berisi tombol verifikasi, dan masa
 * berlaku tautan 30 menit (config('verification.expire')).
 *
 * Mewarisi Illuminate\Auth\Notifications\VerifyEmail sehingga
 * pembuatan signed URL tetap ditangani framework.
 */
class VerifyEmailNotification extends BaseVerifyEmail
{
    use Queueable;

    /**
     * Kirim email hanya bila SMTP sudah diaktifkan Admin —
     * tanpa SMTP, tautan verifikasi tidak bisa sampai ke user.
     */
    public function via(mixed $notifiable): array
    {
        return Setting::get('smtp_enabled') === '1' ? ['mail'] : [];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifikasi Email Anda — '.company_name())
            ->view('emails.verify-email', [
                'user'            => $notifiable,
                'verificationUrl' => $this->verificationUrl($notifiable),
            ]);
    }
}

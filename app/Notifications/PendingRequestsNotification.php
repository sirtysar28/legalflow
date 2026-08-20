<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Notifikasi rutin ke Admin: daftar pengajuan yang masih DRAFT
 * (dibuat tetapi belum diajukan / "belum masuk" ke proses Legal).
 *
 * Dikirim melalui database (in-app) dan email (template modern
 * dengan tabel daftar pengajuan) bila SMTP aktif.
 *
 * Catatan: sengaja TIDAK implement ShouldQueue — aplikasi ini
 * mengirim notifikasi sinkron (tidak ada queue worker di produksi).
 */
class PendingRequestsNotification extends Notification
{
    use Queueable;

    /**
     * @param array<int, array{number: string, title: string, owner: string, department: string, type: string, age: string, url: string}> $requests
     */
    public function __construct(
        public array $requests,
        public string $url
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Email aktif bila SMTP sudah dikonfigurasi & diaktifkan Admin.
        if (Setting::get('smtp_enabled') === '1' && Setting::get('notifications_email_enabled', '1') === '1') {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Ada '.count($this->requests).' Pengajuan Belum Masuk — '.company_name())
            ->view('emails.pending-requests', [
                'greeting' => $notifiable->name ?? 'Admin',
                'requests' => $this->requests,
                'url'      => $this->url,
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        $total = count($this->requests);
        $first = $this->requests[0]['number'] ?? '';

        return [
            'title'   => 'Pengajuan Belum Masuk',
            'message' => $total === 1
                ? "Pengajuan {$first} masih Draft dan belum diajukan ke Legal."
                : "{$total} pengajuan masih Draft dan belum diajukan ke Legal.",
            'url'     => $this->url,
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

<?php

namespace App\Notifications;

use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ApplicationNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $title,
        public string $message,
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
        // Jenis notifikasi -> warna & label pada template email.
        $level = match (true) {
            str_contains($this->title, 'Disetujui') || str_contains($this->title, 'Setujui') => 'success',
            str_contains($this->title, 'Ditolak') => 'danger',
            str_contains($this->title, 'Revisi') => 'warning',
            default => 'info',
        };

        $palette = [
            'info'    => ['color' => '#2d5da8', 'bg' => '#e8eef9', 'label' => 'Notifikasi'],
            'success' => ['color' => '#16a34a', 'bg' => '#e3f6ec', 'label' => 'Disetujui'],
            'warning' => ['color' => '#d97706', 'bg' => '#fdf3dd', 'label' => 'Perlu Tindakan'],
            'danger'  => ['color' => '#dc2626', 'bg' => '#fdecec', 'label' => 'Ditolak'],
        ][$level];

        return (new MailMessage)
            ->subject("{$this->title} — ".company_name())
            ->view('emails.application', [
                'greeting'  => $notifiable->name ?? 'Pengguna',
                'title'     => $this->title,
                'body'      => $this->message,
                'url'       => $this->url,
                'cta'       => 'Lihat Pengajuan',
                'levelLabel' => strtoupper($palette['label']),
                'color'     => $palette['color'],
                'bgColor'   => $palette['bg'],
            ]);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'url' => $this->url,
        ];
    }
}

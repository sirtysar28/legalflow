<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Tandai semua notifikasi milik user sebagai sudah dibaca.
     */
    public function markAllRead(Request $request)
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back();
    }

    /**
     * Buka satu notifikasi: tandai sudah dibaca lalu arahkan
     * ke halaman tujuan (detail pengajuan / antrean review / dll).
     */
    public function read(DatabaseNotification $notification)
    {
        $user = auth()->user();

        // Pastikan notifikasi ini milik user yang login.
        abort_unless(
            $notification->notifiable_id === $user->id
            && $notification->notifiable_type === $user::class,
            403,
            'Notifikasi tidak ditemukan.'
        );

        if ($notification->unread()) {
            $notification->markAsRead();
        }

        return redirect($notification->data['url'] ?? route('dashboard'));
    }
}

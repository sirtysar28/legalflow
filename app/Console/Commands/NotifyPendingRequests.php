<?php

namespace App\Console\Commands;

use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Role;
use App\Models\User;
use App\Notifications\PendingRequestsNotification;
use Illuminate\Console\Command;

/**
 * Kirim pengingat ke Admin untuk pengajuan yang masih DRAFT
 * ("belum masuk" ke proses Legal).
 *
 * Dijadwalkan harian lewat routes/console.php, dan bisa juga
 * dijalankan manual:
 *
 *   php artisan app:notify-pending-requests
 *   php artisan app:notify-pending-requests --hours=48
 */
class NotifyPendingRequests extends Command
{
    /**
     * Ambang umur draft (jam) sebelum diingatkan.
     * Draft yang lebih baru dari ini dianggap masih wajar dikerjakan.
     */
    public const DEFAULT_THRESHOLD_HOURS = 24;

    protected $signature = 'app:notify-pending-requests {--hours= : Ambang umur draft dalam jam (default 24)}';

    protected $description = 'Kirim pengingat ke Admin untuk pengajuan DRAFT yang belum diajukan ke Legal';

    public function handle(): int
    {
        $hours = max(1, (int) ($this->option('hours') ?: self::DEFAULT_THRESHOLD_HOURS));
        $threshold = now()->subHours($hours);

        // Pengajuan DRAFT tua yang belum pernah diajukan (submitted_at NULL).
        $drafts = Application::query()
            ->status(ApplicationStatus::DRAFT)
            ->whereNull('submitted_at')
            ->where('created_at', '<', $threshold)
            ->with(['user', 'department', 'permitType'])
            ->orderBy('created_at')
            ->get();

        if ($drafts->isEmpty()) {
            $this->info("Tidak ada pengajuan DRAFT yang melebihi {$hours} jam. Tidak ada notifikasi dikirim.");

            return self::SUCCESS;
        }

        $url = route('applications.index', ['status' => ApplicationStatus::DRAFT->value]);

        // Susun payload ringkas untuk notifikasi & email.
        $requests = $drafts->map(fn (Application $application) => [
            'number'     => $application->application_number,
            'title'      => $application->title,
            'type'       => $application->permitType?->name ?? $application->application_type->label(),
            'owner'      => $application->user?->name ?? '(pemohon dihapus)',
            'department' => $application->department?->name ?? '',
            'age'        => $application->created_at->diffForHumans(now(), short: true),
            'url'        => $url,
        ])->all();

        // Kirim ke semua Admin & Super Admin aktif.
        $admins = User::query()
            ->whereHas('role', fn ($query) => $query->whereIn('name', [
                Role::NAME_ADMIN,
                Role::NAME_SUPER_ADMIN,
            ]))
            ->where('status', 'active')
            ->get();

        if ($admins->isEmpty()) {
            $this->warn('Tidak ada Admin aktif untuk dinotifikasi.');

            return self::SUCCESS;
        }

        $admins->each(fn (User $admin) => $admin->notify(
            new PendingRequestsNotification($requests, $url)
        ));

        $this->info(sprintf(
            'Notifikasi "%d pengajuan belum masuk" dikirim ke %d admin (draft > %d jam).',
            count($requests),
            $admins->count(),
            $hours,
        ));

        return self::SUCCESS;
    }
}

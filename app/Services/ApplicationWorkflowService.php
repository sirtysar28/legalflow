<?php

namespace App\Services;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\ApplicationHistory;
use App\Models\ApplicationReview;
use App\Models\Document;
use App\Models\User;
use App\Notifications\ApplicationNotification;
use Illuminate\Support\Facades\DB;

/**
 * Application Workflow Service
 *
 * Semua perpindahan status pengajuan HARUS melalui service ini agar
 * otomatis: update status + simpan history + simpan actor + timestamp
 * + catatan + kirim notifikasi ke pihak terkait (audit trail terjaga).
 */
class ApplicationWorkflowService
{
    /**
     * Peta transisi status yang valid.
     */
    public const TRANSITIONS = [
        ApplicationStatus::DRAFT->value => [ApplicationStatus::SUBMITTED->value],
        ApplicationStatus::SUBMITTED->value => [ApplicationStatus::UNDER_REVIEW->value],
        ApplicationStatus::UNDER_REVIEW->value => [
            ApplicationStatus::APPROVED->value,
            ApplicationStatus::REVISION_REQUESTED->value,
            ApplicationStatus::REJECTED->value,
        ],
        ApplicationStatus::REVISION_REQUESTED->value => [ApplicationStatus::RESUBMITTED->value],
        ApplicationStatus::RESUBMITTED->value => [ApplicationStatus::UNDER_REVIEW->value],
        ApplicationStatus::APPROVED->value => [],
        ApplicationStatus::REJECTED->value => [],
        ApplicationStatus::EXPIRED->value => [],
    ];

    public function canTransition(Application $application, ApplicationStatus $to): bool
    {
        $allowed = self::TRANSITIONS[$application->status->value] ?? [];

        return in_array($to->value, $allowed, true);
    }

    /**
     * Jalankan transisi status + catat history + kirim notifikasi.
     */
    public function apply(
        Application $application,
        ApplicationStatus $to,
        User $actor,
        string $action,
        ?string $notes = null
    ): Application {
        if (! $this->canTransition($application, $to)) {
            throw new \InvalidArgumentException(
                "Transisi status dari {$application->status->value} ke {$to->value} tidak diperbolehkan."
            );
        }

        return DB::transaction(function () use ($application, $to, $actor, $action, $notes) {
            $oldStatus = $application->status;

            $application->status = $to;
            $this->setTimestamps($application, $to);
            $application->save();

            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id' => $actor->id,
                'action' => $action,
                'old_status' => $oldStatus->value,
                'new_status' => $to->value,
                'notes' => $notes,
            ]);

            $this->notifyRelevantParties($application, $oldStatus, $to, $action, $notes);

            return $application->fresh();
        });
    }

    private function setTimestamps(Application $application, ApplicationStatus $to): void
    {
        match ($to) {
            ApplicationStatus::SUBMITTED,
            ApplicationStatus::RESUBMITTED => $application->submitted_at = now(),
            ApplicationStatus::APPROVED => $application->approved_at = now(),
            ApplicationStatus::REJECTED => $application->rejected_at = now(),
            default => null,
        };
    }

    /**
     * Ajukan pengajuan (DRAFT -> SUBMITTED) oleh pemilik.
     */
    public function submit(Application $application, User $actor): Application
    {
        return $this->apply(
            $application,
            ApplicationStatus::SUBMITTED,
            $actor,
            'Pengajuan diajukan ke Legal',
            'Pengajuan masuk antrean review Legal/Admin.'
        );
    }

    /**
     * Ajukan ulang setelah revisi (REVISION_REQUESTED -> RESUBMITTED).
     */
    public function resubmit(Application $application, User $actor): Application
    {
        return $this->apply(
            $application,
            ApplicationStatus::RESUBMITTED,
            $actor,
            'Pengajuan diajukan ulang setelah revisi'
        );
    }

    /**
     * Legal/Admin mulai review (SUBMITTED|RESUBMITTED -> UNDER_REVIEW).
     */
    public function startReview(Application $application, User $reviewer): Application
    {
        $application = $this->apply(
            $application,
            ApplicationStatus::UNDER_REVIEW,
            $reviewer,
            'Legal/Admin mulai review'
        );

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'action' => ApplicationReview::ACTION_START,
            'status' => ApplicationStatus::UNDER_REVIEW->value,
            'reviewed_at' => now(),
        ]);

        return $application;
    }

    /**
     * Keputusan Legal: setujui (UNDER_REVIEW -> APPROVED)
     * + dokumen diterbitkan + tersimpan otomatis ke folder divisi.
     *
     * Masa berlaku (valid_until):
     * - AGREEMENT: otomatis dari field "tanggal_selesai" (boleh dioverride reviewer).
     * - PERMIT: diisi reviewer (opsional) — kosong berarti tanpa batas waktu.
     */
    public function approve(Application $application, User $reviewer, ?string $notes = null, ?string $validUntil = null): Application
    {
        if (! $validUntil && $application->application_type === ApplicationType::AGREEMENT) {
            $validUntil = $application->fields->firstWhere('field_name', 'tanggal_selesai')?->field_value;
        }

        $application->valid_until = filled($validUntil) ? $validUntil : null;

        $application = $this->apply(
            $application,
            ApplicationStatus::APPROVED,
            $reviewer,
            'Pengajuan disetujui',
            $notes
        );

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'action' => ApplicationReview::ACTION_APPROVE,
            'status' => ApplicationStatus::APPROVED->value,
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);

        $this->issueDocuments($application);

        return $application;
    }

    /**
     * Keputusan Legal: minta revisi (UNDER_REVIEW -> REVISION_REQUESTED).
     */
    public function requestRevision(Application $application, User $reviewer, string $notes): Application
    {
        $application = $this->apply(
            $application,
            ApplicationStatus::REVISION_REQUESTED,
            $reviewer,
            'Legal/Admin meminta revisi',
            $notes
        );

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'action' => ApplicationReview::ACTION_REVISION,
            'status' => ApplicationStatus::REVISION_REQUESTED->value,
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);

        return $application;
    }

    /**
     * Keputusan Legal: tolak (UNDER_REVIEW -> REJECTED).
     */
    public function reject(Application $application, User $reviewer, string $notes): Application
    {
        $application = $this->apply(
            $application,
            ApplicationStatus::REJECTED,
            $reviewer,
            'Pengajuan ditolak',
            $notes
        );

        ApplicationReview::create([
            'application_id' => $application->id,
            'reviewer_id' => $reviewer->id,
            'action' => ApplicationReview::ACTION_REJECT,
            'status' => ApplicationStatus::REJECTED->value,
            'notes' => $notes,
            'reviewed_at' => now(),
        ]);

        return $application;
    }

    /**
     * Dokumen terbit/disetujui: status ISSUED + tersimpan otomatis ke
     * folder divisi: Document Management/{Divisi}/{Perizinan|Agreement}/...
     */
    private function issueDocuments(Application $application): void
    {
        $category = $application->application_type === ApplicationType::PERMIT
            ? 'Perizinan'
            : 'Agreement';

        $folder = implode('/', [
            'Document Management',
            $application->department?->name ?? 'Tanpa Divisi',
            $category,
            $application->application_number,
        ]);

        foreach ($application->documents as $document) {
            $document->update([
                'status' => Document::STATUS_ISSUED,
                'folder' => $folder,
            ]);
        }
    }

    private function notifyRelevantParties(
        Application $application,
        ApplicationStatus $oldStatus,
        ApplicationStatus $newStatus,
        string $action,
        ?string $notes
    ): void {
        $url = route('applications.show', $application);
        $owner = $application->user;
        $number = $application->application_number;

        // Notifikasi ke pemilik pengajuan untuk aksi Legal/Admin.
        if ($owner && $owner->id !== auth()->id()) {
            match ($newStatus) {
                ApplicationStatus::UNDER_REVIEW => rescue(fn () => $owner->notify(new ApplicationNotification(
                    'Pengajuan Sedang Direview',
                    "{$number} sedang direview oleh Legal/Admin.",
                    $url
                ))),
                ApplicationStatus::REVISION_REQUESTED => rescue(fn () => $owner->notify(new ApplicationNotification(
                    'Revisi Diperlukan',
                    "{$number} diminta revisi: {$notes}",
                    $url
                ))),
                ApplicationStatus::APPROVED => rescue(fn () => $owner->notify(new ApplicationNotification(
                    'Pengajuan Disetujui',
                    "{$number} telah disetujui. Dokumen terbit & tersimpan di folder divisi.",
                    $url
                ))),
                ApplicationStatus::REJECTED => rescue(fn () => $owner->notify(new ApplicationNotification(
                    'Pengajuan Ditolak',
                    "{$number} ditolak. Alasan: {$notes}",
                    $url
                ))),
                default => null,
            };
        }

        // Notifikasi ke Legal/Admin saat user mengajukan / mengajukan ulang.
        if (in_array($newStatus, [ApplicationStatus::SUBMITTED, ApplicationStatus::RESUBMITTED], true)) {
            $message = $newStatus === ApplicationStatus::SUBMITTED
                ? "Pengajuan baru {$number} menunggu review."
                : "Pengajuan {$number} diajukan ulang dan menunggu review.";

            User::whereHas('role', fn ($q) => $q->whereIn('name', ['legal', 'admin']))
                ->where('id', '!=', auth()->id())
                ->get()
                ->each(fn (User $user) => rescue(fn () => $user->notify(
                    new ApplicationNotification('Pengajuan Masuk', $message, $url)
                )));
        }
    }
}

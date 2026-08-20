<?php

namespace App\Services;

use App\Enums\AccessStatus;
use App\Models\Application;
use App\Models\Document;
use App\Models\DocumentAccessRequest;
use App\Models\User;

class AccessService
{
    /**
     * Apakah user boleh melihat / mengunduh dokumen dari pengajuan ini?
     * - Pemilik pengajuan: selalu boleh
     * - Legal / Admin: selalu boleh
     * - User lain: boleh jika ada permintaan akses ACCESS_APPROVED yang masih berlaku
     */
    public static function canAccessDocuments(User $user, Application $application): bool
    {
        if ($application->isOwnedBy($user) || $user->canReview()) {
            return true;
        }

        return self::hasGrantedAccess($user, $application);
    }

    public static function hasGrantedAccess(User $user, Application $application): bool
    {
        return DocumentAccessRequest::query()
            ->where('application_id', $application->id)
            ->where('requested_by', $user->id)
            ->where('status', AccessStatus::APPROVED->value)
            ->whereDate('expired_at', '>=', now()->toDateString())
            ->exists();
    }

    /**
     * Otorisasi unduh dokumen tunggal (termasuk sinkronisasi status EXPIRED).
     */
    public static function authorizeDocumentDownload(User $user, Document $document): bool
    {
        DocumentAccessRequest::syncExpiry();

        return self::canAccessDocuments($user, $document->application);
    }
}

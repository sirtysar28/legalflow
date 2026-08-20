<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    case DRAFT = 'DRAFT';
    case SUBMITTED = 'SUBMITTED';
    case UNDER_REVIEW = 'UNDER_REVIEW';
    case REVISION_REQUESTED = 'REVISION_REQUESTED';
    case RESUBMITTED = 'RESUBMITTED';
    case APPROVED = 'APPROVED';
    case REJECTED = 'REJECTED';
    case EXPIRED = 'EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Diajukan ke Legal',
            self::UNDER_REVIEW => 'Sedang Direview',
            self::REVISION_REQUESTED => 'Perlu Revisi',
            self::RESUBMITTED => 'Diajukan Ulang',
            self::APPROVED => 'Disetujui',
            self::REJECTED => 'Ditolak',
            self::EXPIRED => 'Kadaluarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'secondary',
            self::SUBMITTED => 'info',
            self::UNDER_REVIEW => 'primary',
            self::REVISION_REQUESTED => 'warning',
            self::RESUBMITTED => 'info',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::EXPIRED => 'dark',
        };
    }

    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }
        return $options;
    }
}

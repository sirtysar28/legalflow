<?php

namespace App\Enums;

enum AccessStatus: string
{
    case REQUESTED = 'ACCESS_REQUESTED';
    case APPROVED = 'ACCESS_APPROVED';
    case REJECTED = 'ACCESS_REJECTED';
    case EXPIRED = 'ACCESS_EXPIRED';

    public function label(): string
    {
        return match ($this) {
            self::REQUESTED => 'Menunggu Persetujuan',
            self::APPROVED => 'Akses Disetujui',
            self::REJECTED => 'Akses Ditolak',
            self::EXPIRED => 'Akses Kedaluwarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::REQUESTED => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::EXPIRED => 'secondary',
        };
    }
}

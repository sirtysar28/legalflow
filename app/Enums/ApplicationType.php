<?php

namespace App\Enums;

enum ApplicationType: string
{
    case PERMIT = 'PERMIT';
    case AGREEMENT = 'AGREEMENT';

    public function label(): string
    {
        return match ($this) {
            self::PERMIT => 'Pengajuan Izin',
            self::AGREEMENT => 'Agreement',
        };
    }

    public function prefix(): string
    {
        return match ($this) {
            self::PERMIT => 'PRM',
            self::AGREEMENT => 'AGR',
        };
    }
}

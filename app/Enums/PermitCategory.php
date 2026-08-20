<?php

namespace App\Enums;

enum PermitCategory: string
{
    case USAHA = 'USAHA';
    case BANGUNAN = 'BANGUNAN';
    case LINGKUNGAN = 'LINGKUNGAN';
    case PRODUK = 'PRODUK';
    case OPERASIONAL = 'OPERASIONAL';

    public function label(): string
    {
        return match ($this) {
            self::USAHA => 'Perizinan Usaha',
            self::BANGUNAN => 'Perizinan Bangunan',
            self::LINGKUNGAN => 'Perizinan Lingkungan',
            self::PRODUK => 'Perizinan Produk',
            self::OPERASIONAL => 'Perizinan Operasional',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::USAHA => 'primary',
            self::BANGUNAN => 'info',
            self::LINGKUNGAN => 'success',
            self::PRODUK => 'warning',
            self::OPERASIONAL => 'secondary',
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

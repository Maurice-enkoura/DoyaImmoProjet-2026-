<?php

namespace App\Enums;

enum RoleEnum: string
{
    case ADMIN = 'admin';
    case PARTICULIER = 'particulier';
    case AGENCE = 'agence';

    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrateur',
            self::PARTICULIER => 'Particulier',
            self::AGENCE => 'Agence immobilière',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
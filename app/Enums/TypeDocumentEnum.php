<?php

namespace App\Enums;

enum TypeDocumentEnum: string
{
    case RCCM = 'rccm';
    case NINEA = 'ninea';
    case PIECE_IDENTITE = 'piece_identite';
    case LOGO = 'logo';

    public function label(): string
    {
        return match($this) {
            self::RCCM => 'RCCM',
            self::NINEA => 'NINEA',
            self::PIECE_IDENTITE => 'Pièce d\'identité',
            self::LOGO => 'Logo',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
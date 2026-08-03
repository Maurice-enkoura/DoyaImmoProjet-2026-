<?php

namespace App\Enums;

enum StatutSignalementEnum: string
{
    case EN_ATTENTE = 'en_attente';
    case TRAITE = 'traite';
    case REJETE = 'rejete';

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::TRAITE => 'Traité',
            self::REJETE => 'Rejeté',
        };
    }

    public function badge(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'warning',
            self::TRAITE => 'success',
            self::REJETE => 'danger',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
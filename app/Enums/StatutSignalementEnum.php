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

    public static function labels(): array
    {
        $labels = [];
        foreach (self::cases() as $case) {
            $labels[$case->value] = $case->label();
        }
        return $labels;
    }
}
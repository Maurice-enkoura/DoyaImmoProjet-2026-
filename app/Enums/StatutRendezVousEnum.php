<?php

namespace App\Enums;

enum StatutRendezVousEnum: string
{
    case PLANIFIE      = 'planifie';
    case CONFIRME      = 'confirme';
    case ANNULE        = 'annule';
    case TERMINE       = 'termine';
    case CLIENT_ABSENT = 'client_absent'; // ✅ NOUVEAU

    public function label(): string
    {
        return match($this) {
            self::PLANIFIE      => 'Planifié',
            self::CONFIRME      => 'Confirmé',
            self::ANNULE        => 'Annulé',
            self::TERMINE       => 'Terminé',
            self::CLIENT_ABSENT => 'Client absent', // ✅ NOUVEAU
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

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
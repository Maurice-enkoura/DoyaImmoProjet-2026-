<?php

namespace App\Enums;

enum StatutPropositionEnum: string
{
    case EN_ATTENTE = 'en_attente';
    case ACCEPTEE = 'acceptee';
    case REFUSEE = 'refusee';
    case TERMINEE = 'terminee'; // ✅ Ajouter ce cas

    public function label(): string
    {
        return match($this) {
            self::EN_ATTENTE => 'En attente',
            self::ACCEPTEE => 'Acceptée',
            self::REFUSEE => 'Refusée',
            self::TERMINEE => 'Terminée',
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
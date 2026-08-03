<?php

namespace App\Enums;

enum TypeContratEnum: string
{
    case VENTE = 'vente';
    case LOCATION = 'location';

    public function label(): string
    {
        return match($this) {
            self::VENTE => 'Vente',
            self::LOCATION => 'Location',
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
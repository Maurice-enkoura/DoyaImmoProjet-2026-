<?php

namespace App\Enums;

enum TypeBienEnum: string
{
    case APPARTEMENT = 'appartement';
    case STUDIO = 'studio';
    case VILLA = 'villa';
    case MAISON = 'maison';
    case TERRAIN = 'terrain';
    case BUREAU = 'bureau';

    public function label(): string
    {
        return match($this) {
            self::APPARTEMENT => 'Appartement',
            self::STUDIO => 'Studio',
            self::VILLA => 'Villa',
            self::MAISON => 'Maison',
            self::TERRAIN => 'Terrain',
            self::BUREAU => 'Bureau',
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
<?php

namespace App\Enums;

enum TypeOperationEnum: string
{
    case ACHAT = 'achat';
    case LOCATION = 'location';

    public function label(): string
    {
        return match($this) {
            self::ACHAT => 'Achat',
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
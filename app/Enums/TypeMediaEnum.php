<?php

namespace App\Enums;

enum TypeMediaEnum: string
{
    case IMAGE = 'image';
    case VIDEO = 'video';

    public function label(): string
    {
        return match($this) {
            self::IMAGE => 'Image',
            self::VIDEO => 'Vidéo',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
<?php

namespace App\Enums;

enum MotifSignalementEnum: string
{
    case FRAUDE = 'fraude';
    case CONTENU_INAPPROPRIE = 'contenu_inapproprie';
    case ARNAQUE = 'arnaque';
    case INFORMATIONS_ERRONEES = 'informations_erronees';
    case DOUBLE_ANNONCE = 'double_annonce';
    case AUTRE = 'autre';

    public function label(): string
    {
        return match($this) {
            self::FRAUDE => 'Fraude',
            self::CONTENU_INAPPROPRIE => 'Contenu inapproprié',
            self::ARNAQUE => 'Arnaque',
            self::INFORMATIONS_ERRONEES => 'Informations erronées',
            self::DOUBLE_ANNONCE => 'Double annonce',
            self::AUTRE => 'Autre',
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
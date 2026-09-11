<?php

namespace App\Enums;

enum MotifSignalementEnum: string
{
    case FRAUDE = 'fraude';
    case ARNAQUE = 'arnaque';
    case CONTENU_INAPPROPRIE = 'contenu_inapproprie';
    case FAUSSE_ANNONCE = 'fausse_annonce';
    case COMPORTEMENT_INAPPROPRIE = 'comportement_inapproprié';
    case AUTRE = 'autre';

    public function label(): string
    {
        return match($this) {
            self::FRAUDE => 'Fraude',
            self::ARNAQUE => 'Arnaque',
            self::CONTENU_INAPPROPRIE => 'Contenu inapproprié',
            self::FAUSSE_ANNONCE => 'Fausse annonce',
            self::COMPORTEMENT_INAPPROPRIE => 'Comportement inapproprié',
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
}
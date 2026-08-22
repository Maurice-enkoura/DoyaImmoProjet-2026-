<?php

namespace App\Enums;

enum FormuleAbonnementEnum: string
{
    case BASIC = 'basic';
    case PREMIUM = 'premium';
    case PRO = 'pro';

    public function label(): string
    {
        return match($this) {
            self::BASIC => 'Basique',
            self::PREMIUM => 'Premium',
            self::PRO => 'Pro',
        };
    }

    public function prix(): float
    {
        return match($this) {
            self::BASIC => 0,
            self::PREMIUM => 200,
            self::PRO => 500,
        };
    }

    public function prixMensuel(): string
    {
        return match($this) {
            self::BASIC => 'Gratuit',
            self::PREMIUM => '200 FCFA',
            self::PRO => '500 FCFA',
        };
    }

    public function limiteOffres(): int
    {
        return match($this) {
            self::BASIC => 5,
            self::PREMIUM => 20,
            self::PRO => PHP_INT_MAX,
        };
    }

    public function limiteVedettes(): int
    {
        return match($this) {
            self::BASIC => 0,
            self::PREMIUM => 3,
            self::PRO => PHP_INT_MAX,
        };
    }

    public function badge(): ?string
    {
        return match($this) {
            self::BASIC => null,
            self::PREMIUM => 'Populaire',
            self::PRO => 'Recommandé',
        };
    }

    public function couleur(): string
    {
        return match($this) {
            self::BASIC => '#6A7280',
            self::PREMIUM => '#B5502A',
            self::PRO => '#D4AF37',
        };
    }

    public function icone(): string
    {
        return match($this) {
            self::BASIC => 'fa-regular fa-star',
            self::PREMIUM => 'fa-solid fa-crown',
            self::PRO => 'fa-solid fa-gem',
        };
    }

    public function fonctionnalites(): array
    {
        $offres = $this->limiteOffres() === PHP_INT_MAX ? 'Illimité' : $this->limiteOffres();
        $vedettes = $this->limiteVedettes() === PHP_INT_MAX ? 'Illimité' : $this->limiteVedettes();

        $features = [
            "{$offres} offres envoyées / mois",
            "{$vedettes} biens en vedette",
        ];

        if ($this !== self::BASIC) {
            $features[] = 'Badge "Agence ' . $this->label() . '"';
            $features[] = 'Accès anticipé aux nouveaux besoins';
            $features[] = 'Paiement mensuel automatique';
        }

        return $features;
    }

    public function getDuree(): string
    {
        return '1 mois';
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }


/**
 * Accès anticipé aux nouveaux besoins (en minutes)
 * Premium : voit 5 min avant Basic
 * Pro : voit 10 min avant Basic
 */
public function accesAnticipe(): ?int
{
    return match($this) {
        self::BASIC => null,
        self::PREMIUM => 5,
        self::PRO => 10,
    };
}
}
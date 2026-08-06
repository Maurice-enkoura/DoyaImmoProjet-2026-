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
            self::BASIC => 'Basic',
            self::PREMIUM => 'Premium',
            self::PRO => 'Pro',
        };
    }

    public function prix(): float
    {
        return match($this) {
            self::BASIC => 0,      // Gratuit
            self::PREMIUM => 200,  // 200 FCFA / mois
            self::PRO => 500,      // 500 FCFA / mois
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

    public function prixAnnuel(): string
    {
        return match($this) {
            self::BASIC => 'Gratuit',
            self::PREMIUM => '2 400 FCFA',
            self::PRO => '6 000 FCFA',
        };
    }

    public function limiteBiens(): int
    {
        return match($this) {
            self::BASIC => 5,
            self::PREMIUM => 20,
            self::PRO => PHP_INT_MAX,
        };
    }

    public function getDuree(): string
    {
        return match($this) {
            self::BASIC => '1 mois',
            self::PREMIUM => '1 mois',
            self::PRO => '1 mois',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
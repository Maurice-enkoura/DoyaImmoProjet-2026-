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
            self::BASIC => 0,
            self::PREMIUM => 49.99,
            self::PRO => 99.99,
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

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
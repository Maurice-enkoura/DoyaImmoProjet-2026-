<?php

namespace App\Enums;

enum FormuleAbonnementEnum: string
{
    case BASIC = 'basic';
    case PRO = 'pro'; // ✅ Supprimé PREMIUM

    public function label(): string
    {
        return match($this) {
            self::BASIC => 'Gratuit',
            self::PRO => 'Pro',
        };
    }

    public function prix(): float
    {
        return match($this) {
            self::BASIC => 0,
            self::PRO => 5000, // ✅ 5 000 FCFA
        };
    }

    public function prixMensuel(): string
    {
        return match($this) {
            self::BASIC => '0 FCFA/mois',
            self::PRO => '5 000 FCFA/mois',
        };
    }

    public function limiteOffres(): int
    {
        return match($this) {
            self::BASIC => 5,
            self::PRO => PHP_INT_MAX,
        };
    }

    /**
     * ⚠️ IMPORTANT : La mise en vedette n'est PAS incluse dans l'abonnement.
     * Retourne 0 pour TOUS les abonnements.
     * Les vedettes sont payantes et gérées manuellement par l'admin.
     */
    public function limiteVedettes(): int
    {
        return 0;
    }

    public function badge(): ?string
    {
        return match($this) {
            self::BASIC => null,
            self::PRO => 'Recommandé',
        };
    }

    public function couleur(): string
    {
        return match($this) {
            self::BASIC => '#6A7280',
            self::PRO => '#D4AF37',
        };
    }

    public function icone(): string
    {
        return match($this) {
            self::BASIC => 'fa-regular fa-star',
            self::PRO => 'fa-solid fa-gem',
        };
    }

    public function fonctionnalites(): array
    {
        $offres = $this->limiteOffres() === PHP_INT_MAX ? 'Illimité' : $this->limiteOffres();

        return match($this) {
            self::BASIC => [
                'Création du compte agence',
                'Profil agence',
                'Consultation des demandes',
                'Accès aux demandes pertinentes',
                "{$offres} propositions par mois",
                'Gestion des rendez-vous',
            ],
            self::PRO => [
                ' Tout ce qui est inclus dans Gratuit',
                ' Propositions illimitées',
                ' Publication de biens immobiliers',
                ' Gestion du portefeuille de biens',
                ' Accès complet aux demandes pertinentes',
                ' Réception des demandes',
                ' Gestion des rendez-vous',
                ' Profil agence professionnel',
                ' Statistiques de base',
                'Possibilité de demander une mise en vedette (payant)',
            ],
        };
    }

    public function getDuree(): string
    {
        return '1 mois';
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function accesAnticipe(): ?int
    {
        return match($this) {
            self::BASIC => null,
            self::PRO => 10,
        };
    }
}
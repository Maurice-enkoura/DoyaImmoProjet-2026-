<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\StatutRendezVousEnum;

class RendezVous extends Model
{
    use HasFactory;

    protected $table = 'rendez_vous';

    protected $fillable = [
        'proposition_id',
        'creneau_id',
        'particulier_id',
        'agence_id',
        'date_visite',
        'heure_visite',
        'statut',
    ];

    protected $casts = [
        'statut' => StatutRendezVousEnum::class,
        'date_visite' => 'date',
    ];

    public function proposition(): BelongsTo
    {
        return $this->belongsTo(Proposition::class);
    }

    public function creneau(): BelongsTo
    {
        return $this->belongsTo(CreneauRendezVous::class);
    }

    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function scopePlanifies($query)
    {
        return $query->where('statut', StatutRendezVousEnum::PLANIFIE);
    }

    public function scopeConfirmes($query)
    {
        return $query->where('statut', StatutRendezVousEnum::CONFIRME);
    }

    /**
     * Vérifie si les numéros de téléphone sont visibles
     * Les numéros sont visibles uniquement lorsque le rendez-vous est confirmé
     */
    public function isPhoneVisible(): bool
    {
        return $this->statut === StatutRendezVousEnum::CONFIRME;
    }

    /**
     * Récupère le numéro de téléphone de l'agence (masqué si non confirmé)
     */
    public function getAgencePhoneAttribute(): ?string
    {
        if (!$this->isPhoneVisible()) {
            return null;
        }
        
        return $this->agence?->telephone;
    }

    /**
     * Récupère le numéro de téléphone du particulier (masqué si non confirmé)
     */
    public function getParticulierPhoneAttribute(): ?string
    {
        if (!$this->isPhoneVisible()) {
            return null;
        }
        
        return $this->particulier?->user?->telephone;
    }

    /**
     * Récupère le numéro de téléphone de l'agence formaté (masqué si non confirmé)
     */
    public function getAgencePhoneFormattedAttribute(): string
    {
        if (!$this->isPhoneVisible()) {
            return ' Non disponible';
        }
        
        $phone = $this->agence?->telephone;
        return $phone ? $this->formatPhone($phone) : 'Non renseigné';
    }

    /**
     * Récupère le numéro de téléphone du particulier formaté (masqué si non confirmé)
     */
    public function getParticulierPhoneFormattedAttribute(): string
    {
        if (!$this->isPhoneVisible()) {
            return 'Non disponible';
        }
        
        $phone = $this->particulier?->user?->telephone;
        return $phone ? $this->formatPhone($phone) : 'Non renseigné';
    }

    /**
     * Formate un numéro de téléphone
     */
    private function formatPhone(string $phone): string
    {
        // Supprimer les espaces et caractères non numériques
        $clean = preg_replace('/[^0-9]/', '', $phone);
        
        // Format pour +221 77 123 45 67
        if (strlen($clean) === 12 && substr($clean, 0, 3) === '221') {
            return '+221 ' . substr($clean, 3, 2) . ' ' . substr($clean, 5, 3) . ' ' . substr($clean, 8, 2) . ' ' . substr($clean, 10, 2);
        }
        
        // Format pour 77 123 45 67
        if (strlen($clean) === 9) {
            return substr($clean, 0, 2) . ' ' . substr($clean, 2, 3) . ' ' . substr($clean, 5, 2) . ' ' . substr($clean, 7, 2);
        }
        
        return $phone;
    }

    public function getStatutLabelAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'label')) {
            return $this->statut->label();
        }
        $labels = [
            'planifie' => 'Planifié',
            'confirme' => 'Confirmé',
            'termine' => 'Terminé',
            'annule' => 'Annulé',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }
}
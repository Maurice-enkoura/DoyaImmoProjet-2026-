<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Enums\StatutPropositionEnum;

class Proposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id',
        'agence_id',
        'bien_id',
        'particulier_id',
        'prix_propose',
        'disponibilite',           
        'caution_mois',            
        'avance_loyer_mois',       
        'honoraires',              
        'charges',                 
        'conditions_particulieres',
        'message',
        'statut',
        'date_proposition',
    ];

    protected $casts = [
        'prix_propose'            => 'integer',
        'disponibilite'           => 'date',        
        'caution_mois'            => 'integer',    
        'avance_loyer_mois'       => 'integer',     
        'honoraires'              => 'integer',     
        'charges'                 => 'integer',     
        'date_proposition'        => 'datetime',
        'statut'                  => StatutPropositionEnum::class,
    ];

    // ==================== RELATIONS ====================

    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandeImmobiliere::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(BienImmobilier::class);
    }

    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // ==================== ACCESSORS EXISTANTS ====================

    public function getStatutLabelAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'label')) {
            return $this->statut->label();
        }
        $labels = [
            'en_attente' => 'En attente',
            'acceptee'   => 'Acceptée',
            'refusee'    => 'Refusée',
            'terminee'   => 'Terminée',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    public function getStatutValueAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'value')) {
            return $this->statut->value;
        }
        return is_string($this->statut) ? $this->statut : 'en_attente';
    }

    // ==================== 🆕 ACCESSORS CONDITIONS ====================

    /**
     * Ex : "2 mois" — ou null si non renseigné.
     */
    public function getCautionLabelAttribute(): ?string
    {
        return $this->caution_mois
            ? $this->caution_mois . ' mois'
            : null;
    }

    /**
     * Ex : "1 mois" — avance de loyer.
     */
    public function getAvanceLabelAttribute(): ?string
    {
        return $this->avance_loyer_mois
            ? $this->avance_loyer_mois . ' mois'
            : null;
    }

    /**
     * Ex : "250 000 FCFA".
     */
    public function getHonorairesFormateAttribute(): ?string
    {
        return $this->honoraires !== null
            ? number_format($this->honoraires, 0, ',', ' ') . ' FCFA'
            : null;
    }

    public function getChargesFormateAttribute(): ?string
    {
        return $this->charges !== null
            ? number_format($this->charges, 0, ',', ' ') . ' FCFA'
            : null;
    }

    /**
     * Ex : "1er octobre 2026".
     */
    public function getDisponibiliteLabelAttribute(): ?string
    {
        if (!$this->disponibilite) return null;

        // Si c'est aujourd'hui ou avant → "Maintenant"
        if ($this->disponibilite->isPast() || $this->disponibilite->isToday()) {
            return 'Maintenant';
        }

        return 'À partir du ' . $this->disponibilite->translatedFormat('d F Y');
    }

    /**
     * Montant total à prévoir à l'entrée (info affichée au particulier).
     * Caution + avance + honoraires + charges.
     * ⚠️ N'entre PAS dans le matching — purement informatif.
     */
    public function getCoutEntreeTotalAttribute(): int
    {
        $caution   = ($this->caution_mois ?? 0) * ($this->prix_propose ?? 0);
        $avance    = ($this->avance_loyer_mois ?? 0) * ($this->prix_propose ?? 0);
        $honoraires= $this->honoraires ?? 0;
        $charges   = $this->charges ?? 0;

        return $caution + $avance + $honoraires + $charges;
    }

    public function getCoutEntreeTotalFormateAttribute(): string
    {
        return number_format($this->cout_entree_total, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Vérifie si TOUTES les conditions sont renseignées (utile pour l'affichage).
     */
    public function getAConditionsFinancieresAttribute(): bool
    {
        return $this->caution_mois !== null
            || $this->avance_loyer_mois !== null
            || $this->honoraires !== null
            || $this->charges !== null
            || $this->conditions_particulieres !== null;
    }
}
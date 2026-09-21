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
        'refus_automatique',   // ✅ AJOUT
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
        'refus_automatique'       => 'boolean',   // ✅ AJOUT
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

    // ==================== HELPERS STATUT ====================

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

    // ✅ Helper pratique
    public function isAcceptee(): bool
    {
        return $this->statut_value === StatutPropositionEnum::ACCEPTEE->value;
    }

    public function hasRendezVous(): bool
    {
        return $this->rendezVous()->exists();
    }

    // ==================== ACCESSORS CONDITIONS ====================

    public function getCautionLabelAttribute(): ?string
    {
        return $this->caution_mois
            ? $this->caution_mois . ' mois'
            : null;
    }

    public function getAvanceLabelAttribute(): ?string
    {
        return $this->avance_loyer_mois
            ? $this->avance_loyer_mois . ' mois'
            : null;
    }

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

    public function getDisponibiliteLabelAttribute(): ?string
    {
        if (!$this->disponibilite) return null;

        if ($this->disponibilite->isPast() || $this->disponibilite->isToday()) {
            return 'Maintenant';
        }

        return 'À partir du ' . $this->disponibilite->translatedFormat('d F Y');
    }

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

    public function getAConditionsFinancieresAttribute(): bool
    {
        return $this->caution_mois !== null
            || $this->avance_loyer_mois !== null
            || $this->honoraires !== null
            || $this->charges !== null
            || $this->conditions_particulieres !== null;
    }
}
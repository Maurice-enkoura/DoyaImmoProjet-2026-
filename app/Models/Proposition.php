<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\StatutPropositionEnum;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Proposition extends Model
{
    use HasFactory;

    protected $fillable = [
        'demande_id',
        'agence_id',
        'bien_id',
        'particulier_id',
        'prix_propose',
        'message',
        'statut',
        'date_proposition',
    ];

    protected $casts = [
        'prix_propose' => 'decimal:2',
        'date_proposition' => 'datetime',
        'statut' => StatutPropositionEnum::class,
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

    /**
     * Relation avec les rendez-vous
     */
    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    // ==================== ACCESSORS ====================

    public function getStatutLabelAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'label')) {
            return $this->statut->label();
        }
        $labels = [
            'en_attente' => 'En attente',
            'acceptee' => 'Acceptée',
            'refusee' => 'Refusée',
            'terminee' => 'Terminée',
        ];
        return $labels[$this->statut] ?? $this->statut;
    }

    // ✅ Accesseur pour la valeur du statut
    public function getStatutValueAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'value')) {
            return $this->statut->value;
        }
        
        if (is_string($this->statut)) {
            return $this->statut;
        }
        
        return 'en_attente';
    }


    


/**
 * Relation avec les médias (polymorphique)
 */
public function medias(): MorphMany
{
    return $this->morphMany(Media::class, 'mediable');
}
}
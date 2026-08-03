<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Enums\StatutPropositionEnum;
use App\Models\DemandeImmobiliere;  
use App\Models\Agence;

class Proposition extends Model
{
    use HasFactory;

    protected $table = 'propositions';

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
        'statut' => StatutPropositionEnum::class,
        'prix_propose' => 'decimal:2',
        'date_proposition' => 'datetime',
    ];

    public function demande(): BelongsTo
    {
        return $this->belongsTo(DemandeImmobiliere::class, 'demande_id');
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function bien(): BelongsTo
    {
        return $this->belongsTo(BienImmobilier::class, 'bien_id');
    }

    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    // Ajout de la relation polymorphique pour les médias
    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', StatutPropositionEnum::EN_ATTENTE);
    }

    public function scopeAcceptees($query)
    {
        return $query->where('statut', StatutPropositionEnum::ACCEPTEE);
    }
}
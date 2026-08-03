<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;

class BienImmobilier extends Model
{
    use HasFactory;

    protected $table = 'biens_immobiliers';

    protected $fillable = [
        'agence_id',
        'titre',
        'type_bien',
        'type_contrat',
        'prix',
        'quartier',
        'quartier_id',
        'adresse',
        'nombre_chambres',
        'nombre_salles_bain',
        'surface',
        'parking_disponible',
        'est_meuble',
        'description',
        'statut',
    ];

    protected $casts = [
        'type_bien' => TypeBienEnum::class,
        'type_contrat' => TypeContratEnum::class,
        'prix' => 'decimal:2',
        'surface' => 'decimal:2',
        'parking_disponible' => 'boolean',
        'est_meuble' => 'boolean',
        'statut' => 'boolean',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    // Correction: Spécifier la clé étrangère
    public function propositions(): HasMany
    {
        return $this->hasMany(Proposition::class, 'bien_id');
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    public function scopeDisponibles($query)
    {
        return $query->where('statut', true);
    }

    public function getImagesAttribute()
    {
        return $this->medias()->where('type_media', 'image')->get();
    }

    public function getVideosAttribute()
    {
        return $this->medias()->where('type_media', 'video')->get();
    }

    public function getQuartierNameAttribute(): string
    {
        return $this->quartier ? $this->quartier->nom : $this->quartier;
    }
}
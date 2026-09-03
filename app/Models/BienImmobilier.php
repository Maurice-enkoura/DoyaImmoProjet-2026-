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
    use \App\Traits\Sluggable;

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
        'vues',
        'est_vedette',
        'vedette_debut',
        'vedette_fin',
        'vedette_duree',
        'slug',
    ];
     protected $slugSource = 'titre';

    protected $casts = [
        'type_bien' => TypeBienEnum::class,
        'type_contrat' => TypeContratEnum::class,
        'prix' => 'decimal:2',
        'surface' => 'decimal:2',
        'parking_disponible' => 'boolean',
        'est_meuble' => 'boolean',
        'statut' => 'boolean',
        'est_vedette' => 'boolean',
        'vedette_debut' => 'datetime',
        'vedette_fin' => 'datetime',
         'slug' => 'string',
    ];

    // ==================== RELATIONS ====================
    
    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function medias(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function propositions(): HasMany
    {
        return $this->hasMany(Proposition::class, 'bien_id');
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    // ==================== SCOPES ====================

    public function scopeDisponibles($query)
    {
        return $query->where('statut', true);
    }

    public function scopeVedette($query)
    {
        return $query->where('est_vedette', true)
            ->where('vedette_fin', '>', now());
    }

    public function scopeVedetteExpire($query)
    {
        return $query->where('est_vedette', true)
            ->where('vedette_fin', '<=', now());
    }

    // ==================== ACCESSORS ====================

    public function getImagesAttribute()
    {
        return $this->medias()->where('type_media', 'image')->get();
    }

    public function getVideosAttribute()
    {
        return $this->medias()->where('type_media', 'video')->get();
    }

    public function getTypeContratLabelAttribute()
    {
        if (is_object($this->type_contrat) && method_exists($this->type_contrat, 'label')) {
            return $this->type_contrat->label();
        }
        return $this->type_contrat;
    }

    public function getTypeBienLabelAttribute()
    {
        if (is_object($this->type_bien) && method_exists($this->type_bien, 'label')) {
            return $this->type_bien->label();
        }
        return $this->type_bien;
    }

    public function getQuartierNomAttribute(): string
    {
        if ($this->quartier) {
            if (is_object($this->quartier)) {
                return $this->quartier->nom ?? 'N/A';
            }
            return $this->quartier;
        }
        if ($this->quartier_id) {
            $quartier = Quartier::find($this->quartier_id);
            if ($quartier) {
                return $quartier->nom;
            }
        }
        return 'N/A';
    }

    public function getTitreWithContratAttribute(): string
    {
        $titre = $this->titre ?? 'N/A';
        $contrat = $this->type_contrat_label;
        if ($contrat && $contrat !== 'N/A') {
            return $titre . ' (' . $contrat . ')';
        }
        return $titre;
    }

    // ==================== VEDETTE ====================

    public function getEstVedetteAttribute($value)
    {
        if ($value && $this->vedette_fin && $this->vedette_fin <= now()) {
            $this->update(['est_vedette' => false]);
            return false;
        }
        return $value;
    }

    /**
     * Récupère le nombre de jours restants pour la vedette
     * Retourne un entier et gère les cas où il reste moins d'un jour
     */
    public function getVedetteJoursRestantsAttribute(): int
    {
        if (!$this->est_vedette || !$this->vedette_fin) {
            return 0;
        }
        
        // Si la date est déjà passée
        if ($this->vedette_fin->isPast()) {
            return 0;
        }
        
        // Calculer la différence en jours
        $diff = now()->diffInDays($this->vedette_fin, true);
        
        // Si la différence est de 0 (moins de 24h), retourner 1
        if ($diff < 1 && $this->vedette_fin->isFuture()) {
            return 1;
        }
        
        return (int) floor($diff);
    }

    /**
     * Récupère le libellé des jours restants
     */
    public function getVedetteJoursLabelAttribute(): string
    {
        $jours = $this->vedette_jours_restants;
        
        if (!$this->est_vedette || $this->vedette_fin->isPast()) {
            return 'Expirée';
        }
        
        if ($jours === 0) {
            return 'Aujourd\'hui';
        }
        
        if ($jours === 1) {
            return '1 jour restant';
        }
        
        return $jours . ' jours restants';
    }

    public function getVedettePourcentageAttribute(): int
    {
        if (!$this->est_vedette || !$this->vedette_debut || !$this->vedette_fin) {
            return 0;
        }
        
        $total = $this->vedette_debut->diffInDays($this->vedette_fin);
        if ($total <= 0) return 0;
        
        $ecoule = $this->vedette_debut->diffInDays(now());
        if ($ecoule >= $total) {
            return 100;
        }
        
        $pourcentage = ($ecoule / $total) * 100;
        return (int) round(min(100, max(0, $pourcentage)));
    }
    /**
 * Get the route key for the model.
 */
public function getRouteKeyName(): string
{
    return 'slug';
}
}
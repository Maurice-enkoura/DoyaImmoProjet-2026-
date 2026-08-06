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
        'vues',
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

    // ==================== ACCESSORS ====================

    public function getImagesAttribute()
    {
        return $this->medias()->where('type_media', 'image')->get();
    }

    public function getVideosAttribute()
    {
        return $this->medias()->where('type_media', 'video')->get();
    }

    // Accesseur pour le type de contrat (affichage)
    public function getTypeContratLabelAttribute()
    {
        if (is_object($this->type_contrat) && method_exists($this->type_contrat, 'label')) {
            return $this->type_contrat->label();
        }
        return $this->type_contrat;
    }

    // Accesseur pour le type de bien (affichage)
    public function getTypeBienLabelAttribute()
    {
        if (is_object($this->type_bien) && method_exists($this->type_bien, 'label')) {
            return $this->type_bien->label();
        }
        return $this->type_bien;
    }

    // Accesseur pour le nom du quartier
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

    // Accesseur pour afficher le type de contrat avec le titre
    public function getTitreWithContratAttribute(): string
    {
        $titre = $this->titre ?? 'N/A';
        $contrat = $this->type_contrat_label;
        if ($contrat && $contrat !== 'N/A') {
            return $titre . ' (' . $contrat . ')';
        }
        return $titre;
    }
}
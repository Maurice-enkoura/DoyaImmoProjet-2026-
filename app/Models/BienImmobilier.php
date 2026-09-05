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
        'climatisation',
        'balcon',
        'jardin',
        'piscine',
        'ascenseur',
        'securite',
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
        'climatisation' => 'boolean',
        'balcon' => 'boolean',
        'jardin' => 'boolean',
        'piscine' => 'boolean',
        'ascenseur' => 'boolean',
        'securite' => 'boolean',
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

    /**
     * Récupère le statut du bien en texte
     */
    public function getStatutTexteAttribute(): string
    {
        return $this->statut ? 'Disponible' : 'Indisponible';
    }

    /**
     * Récupère la classe CSS du statut
     */
    public function getStatutClasseAttribute(): string
    {
        return $this->statut ? 'disponible' : 'indisponible';
    }

    // ==================== ✅ ÉQUIPEMENTS COMPLETS ====================

    /**
     * Récupère la liste complète des équipements du bien
     */
    public function getEquipementsAttribute(): array
    {
        $equipements = [];
        if ($this->parking_disponible) $equipements[] = 'Parking';
        if ($this->est_meuble) $equipements[] = 'Meublé';
        if ($this->climatisation) $equipements[] = 'Climatisation';
        if ($this->balcon) $equipements[] = 'Balcon';
        if ($this->jardin) $equipements[] = 'Jardin';
        if ($this->piscine) $equipements[] = 'Piscine';
        if ($this->ascenseur) $equipements[] = 'Ascenseur';
        if ($this->securite) $equipements[] = 'Sécurité 24h/24';
        return $equipements;
    }

    /**
     * Vérifie si le bien a un équipement spécifique
     */
    public function hasEquipement(string $equipement): bool
    {
        return in_array($equipement, $this->equipements);
    }

    /**
     * Récupère le nombre d'équipements du bien
     */
    public function getEquipementsCountAttribute(): int
    {
        return count($this->equipements);
    }

    /**
     * Récupère les équipements sous forme de tableau associatif
     */
    public function getEquipementsWithStatusAttribute(): array
    {
        return [
            'parking' => ['label' => '🚗 Parking', 'value' => $this->parking_disponible ?? false],
            'meuble' => ['label' => '🛋️ Meublé', 'value' => $this->est_meuble ?? false],
            'climatisation' => ['label' => '❄️ Climatisation', 'value' => $this->climatisation ?? false],
            'balcon' => ['label' => '🌅 Balcon', 'value' => $this->balcon ?? false],
            'jardin' => ['label' => '🌿 Jardin', 'value' => $this->jardin ?? false],
            'piscine' => ['label' => '🏊 Piscine', 'value' => $this->piscine ?? false],
            'ascenseur' => ['label' => '🛗 Ascenseur', 'value' => $this->ascenseur ?? false],
            'securite' => ['label' => '🛡️ Sécurité', 'value' => $this->securite ?? false],
        ];
    }

    /**
     * Récupère les équipements sous forme de badges HTML
     */
    public function getEquipementsHtmlAttribute(): string
    {
        $equipements = $this->equipements;
        if (empty($equipements)) {
            return '<span class="text-muted">Aucun équipement</span>';
        }
        
        $badges = array_map(function($equipement) {
            return '<span class="badge-equipement">' . $equipement . '</span>';
        }, $equipements);
        
        return implode(' ', $badges);
    }

    // ==================== VEDETTE ====================

    /**
     * Getter pour est_vedette (sans effet de bord)
     */
    public function getEstVedetteAttribute($value)
    {
        // Vérifier si la vedette est expirée mais ne pas modifier la DB
        if ($value && $this->vedette_fin && $this->vedette_fin <= now()) {
            return false;
        }
        return $value;
    }

    /**
     * Vérifier et mettre à jour le statut vedette si expiré
     * À appeler périodiquement (via un job ou un scheduler)
     */
    public function verifierEtMettreAJourVedette(): void
    {
        if ($this->est_vedette && $this->vedette_fin && $this->vedette_fin <= now()) {
            $this->update(['est_vedette' => false]);
        }
    }

    /**
     * Récupère le nombre de jours restants pour la vedette
     */
    public function getVedetteJoursRestantsAttribute(): int
    {
        if (!$this->est_vedette || !$this->vedette_fin) {
            return 0;
        }
        
        if ($this->vedette_fin->isPast()) {
            return 0;
        }
        
        $diff = now()->diffInDays($this->vedette_fin, true);
        
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

    // ==================== MÉTHODES DE COMPATIBILITÉ ====================

    /**
     * Vérifie si le bien est compatible avec une demande
     */
    public function estCompatibleAvec(DemandeImmobiliere $demande): bool
    {
        // 1. Type d'opération
        $typeOperation = $demande->type_operation instanceof \UnitEnum ? $demande->type_operation->value : $demande->type_operation;
        $typeContrat = $this->type_contrat instanceof \UnitEnum ? $this->type_contrat->value : $this->type_contrat;
        
        if ($typeOperation !== $typeContrat) {
            return false;
        }
        
        // 2. Type de bien
        $typeBienDemande = $demande->type_bien instanceof \UnitEnum ? $demande->type_bien->value : $demande->type_bien;
        $typeBienBien = $this->type_bien instanceof \UnitEnum ? $this->type_bien->value : $this->type_bien;
        
        if ($typeBienDemande !== $typeBienBien) {
            return false;
        }
        
        // 3. Zone géographique
        $zoneDemande = trim(strtolower($demande->zone_recherchee ?? ''));
        $zoneBien = trim(strtolower($this->quartier ?? ''));
        
        if (!empty($zoneDemande) && !empty($zoneBien)) {
            if ($zoneDemande !== $zoneBien && 
                strpos($zoneBien, $zoneDemande) === false && 
                strpos($zoneDemande, $zoneBien) === false) {
                return false;
            }
        }
        
        // 4. Budget
        if ($this->prix > $demande->budget_maximum) {
            return false;
        }
        
        return true;
    }

    // ==================== ROUTE KEY ====================
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\TypeOperationEnum;
use App\Enums\TypeBienEnum;
use App\Enums\StatutDemandeEnum;

class DemandeImmobiliere extends Model
{
    use HasFactory;
    use \App\Traits\Sluggable;

    protected $table = 'demande_immobilieres';

    protected $fillable = [
        'particulier_id',
        'type_operation',
        'type_bien',
        'budget_maximum',
        'zone_recherchee',
        'quartier_id',
        'nombre_chambres',
        'nombre_salles_bain',
        'surface_minimum',
        'parking',
        'meuble',
        'climatisation',
        'balcon',
        'jardin',
        'piscine',
        'ascenseur',
        'securite',
        'date_entree_souhaitee',
        'criteres_particuliers',
        'description',
        'statut',
        'date_publication',
        'slug',
    ];
     protected $slugSource = 'titre';

    protected $casts = [
        'type_operation' => TypeOperationEnum::class,
        'type_bien' => TypeBienEnum::class,
        'statut' => StatutDemandeEnum::class,
        'budget_maximum' => 'decimal:2',
        'surface_minimum' => 'decimal:2',
        'nombre_salles_bain' => 'integer',
        'parking' => 'boolean',
        'meuble' => 'boolean',
        'climatisation' => 'boolean',
        'balcon' => 'boolean',
        'jardin' => 'boolean',
        'piscine' => 'boolean',
        'ascenseur' => 'boolean',
        'securite' => 'boolean',
        'date_entree_souhaitee' => 'date',
        'date_publication' => 'datetime',
        'slug' => 'string',
    ];

    // ==================== RELATIONS ====================
    
    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function propositions(): HasMany
    {
        return $this->hasMany(Proposition::class, 'demande_id');
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    // ==================== SCOPES ====================

    public function scopeEnAttente($query)
    {
        return $query->where('statut', StatutDemandeEnum::EN_ATTENTE);
    }

    public function scopeEnCours($query)
    {
        return $query->where('statut', StatutDemandeEnum::EN_COURS);
    }

    // ==================== ACCESSORS ====================

    // Accesseurs pour les libellés des enums
    public function getTypeOperationLabelAttribute()
    {
        return is_object($this->type_operation) ? $this->type_operation->label() : $this->type_operation;
    }

    public function getTypeBienLabelAttribute()
    {
        return is_object($this->type_bien) ? $this->type_bien->label() : $this->type_bien;
    }

    public function getStatutLabelAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'label')) {
            return $this->statut->label();
        }
        
        $labels = [
            'en_attente' => 'En attente',
            'en_cours' => 'En cours',
            'terminee' => 'Terminée',
            'annulee' => 'Annulée',
        ];
        
        return $labels[$this->statut] ?? (string) $this->statut;
    }

    // ✅ CORRECTION : Accesseur pour la valeur du statut
    public function getStatutValueAttribute()
    {
        if (is_object($this->statut) && method_exists($this->statut, 'value')) {
            return $this->statut->value;
        }
        
        // Si c'est une chaîne, la retourner directement
        if (is_string($this->statut)) {
            return $this->statut;
        }
        
        // Fallback
        return 'en_attente';
    }

    // Accesseur pour le nom du quartier
    public function getQuartierNameAttribute(): string
    {
        return $this->quartier ? $this->quartier->nom : $this->zone_recherchee;
    }

    // Accesseur pour le nombre de propositions
    public function getPropositionsCountAttribute()
    {
        return $this->propositions()->count();
    }

    // Accesseur pour la liste des équipements sous forme de tableau
    public function getEquipementsAttribute(): array
    {
        $equipements = [];
        if ($this->parking) $equipements[] = 'Parking';
        if ($this->meuble) $equipements[] = 'Meublé';
        if ($this->climatisation) $equipements[] = 'Climatisation';
        if ($this->balcon) $equipements[] = 'Balcon';
        if ($this->jardin) $equipements[] = 'Jardin';
        if ($this->piscine) $equipements[] = 'Piscine';
        if ($this->ascenseur) $equipements[] = 'Ascenseur';
        if ($this->securite) $equipements[] = 'Sécurité 24h/24';
        return $equipements;
    }

    // Accesseur pour les équipements avec leurs statuts (pour l'affichage)
    public function getEquipementsWithStatusAttribute(): array
    {
        return [
            'parking' => ['label' => ' Parking', 'value' => $this->parking ?? false],
            'meuble' => ['label' => ' Meublé', 'value' => $this->meuble ?? false],
            'climatisation' => ['label' => ' Climatisation', 'value' => $this->climatisation ?? false],
            'balcon' => ['label' => ' Balcon', 'value' => $this->balcon ?? false],
            'jardin' => ['label' => ' Jardin', 'value' => $this->jardin ?? false],
            'piscine' => ['label' => ' Piscine', 'value' => $this->piscine ?? false],
            'ascenseur' => ['label' => ' Ascenseur', 'value' => $this->ascenseur ?? false],
            'securite' => ['label' => ' Sécurité 24h/24', 'value' => $this->securite ?? false],
        ];
    }

    // ==================== MÉTHODES ====================

    /**
     * Calcule le score de matching entre une demande et un bien
     */
    public function calculerScore(BienImmobilier $bien): array
    {
        $score = 0;
        $details = [];
        $criteres = [];

        // === CRITÈRES OBLIGATOIRES ===

        // 1. Type d'opération (OBLIGATOIRE - 18 points)
        $typeOperation = $this->type_operation instanceof \UnitEnum ? $this->type_operation->value : $this->type_operation;
        $typeContrat = $bien->type_contrat instanceof \UnitEnum ? $bien->type_contrat->value : $bien->type_contrat;
        
        if ($typeOperation === $typeContrat) {
            $score += 18;
            $criteres['type_operation'] = true;
            $details['type_operation'] = ' Type d\'opération: ' . $typeOperation;
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['Type d\'opération ne correspond pas']];
        }

        // 2. Type de bien (OBLIGATOIRE - 18 points)
        $typeBien = $this->type_bien instanceof \UnitEnum ? $this->type_bien->value : $this->type_bien;
        $typeBienBien = $bien->type_bien instanceof \UnitEnum ? $bien->type_bien->value : $bien->type_bien;
        
        if ($typeBien === $typeBienBien) {
            $score += 18;
            $criteres['type_bien'] = true;
            $details['type_bien'] = ' Type de bien: ' . $typeBien;
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['Type de bien ne correspond pas']];
        }

        // 3. Zone géographique (OBLIGATOIRE - 18 points)
        if (strtolower(trim($this->zone_recherchee)) === strtolower(trim($bien->quartier))) {
            $score += 18;
            $criteres['zone'] = true;
            $details['zone'] = ' Zone: ' . $this->zone_recherchee;
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['Zone géographique ne correspond pas']];
        }

        // === CRITÈRES DE COMPATIBILITÉ ===

        // 4. Budget (12 points avec tolérance ±20%)
        $tolerance = 0.20;
        $prixBien = floatval($bien->prix);
        $budgetMax = floatval($this->budget_maximum);
        $seuilMax = $budgetMax * (1 + $tolerance);

        if ($prixBien <= $budgetMax) {
            $score += 12;
            $criteres['budget'] = true;
            $details['budget'] = ' Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F ≤ ' . number_format($budgetMax, 0, ',', ' ') . ' F';
        } elseif ($prixBien <= $seuilMax) {
            $score += 7;
            $criteres['budget'] = 'partiel';
            $details['budget'] = ' Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F (tolérance +20%)';
        } else {
            $score += 0;
            $criteres['budget'] = false;
            $details['budget'] = ' Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F > ' . number_format($budgetMax, 0, ',', ' ') . ' F';
        }

        // 5. Surface (8 points avec tolérance ±20%)
        if ($this->surface_minimum) {
            $surfaceMin = floatval($this->surface_minimum);
            $surfaceBien = floatval($bien->surface);
            $surfaceMinTolere = $surfaceMin * 0.8;

            if ($surfaceMin <= $surfaceBien) {
                $score += 8;
                $criteres['surface'] = true;
                $details['surface'] = ' Surface: ' . $surfaceMin . ' m² ≤ ' . $surfaceBien . ' m²';
            } elseif ($surfaceMinTolere <= $surfaceBien) {
                $score += 5;
                $criteres['surface'] = 'partiel';
                $details['surface'] = ' Surface: ' . $surfaceMin . ' m² (tolérance -20%)';
            } else {
                $score += 0;
                $criteres['surface'] = false;
                $details['surface'] = ' Surface: ' . $surfaceMin . ' m² > ' . $surfaceBien . ' m²';
            }
        }

        // 6. Nombre de chambres (8 points avec tolérance -1)
        if ($this->nombre_chambres) {
            $chambresDemande = intval($this->nombre_chambres);
            $chambresBien = intval($bien->nombre_chambres);

            if ($chambresDemande <= $chambresBien) {
                $score += 8;
                $criteres['chambres'] = true;
                $details['chambres'] = ' Chambres: ' . $chambresDemande . ' ≤ ' . $chambresBien;
            } elseif ($chambresDemande - 1 <= $chambresBien) {
                $score += 5;
                $criteres['chambres'] = 'partiel';
                $details['chambres'] = ' Chambres: ' . $chambresDemande . ' (tolérance -1)';
            } else {
                $score += 0;
                $criteres['chambres'] = false;
                $details['chambres'] = ' Chambres: ' . $chambresDemande . ' > ' . $chambresBien;
            }
        }

        // 7. Nombre de salles de bain (8 points avec tolérance -1)
        if ($this->nombre_salles_bain) {
            $sdbDemande = intval($this->nombre_salles_bain);
            $sdbBien = intval($bien->nombre_salles_bain);

            if ($sdbDemande <= $sdbBien) {
                $score += 8;
                $criteres['sdb'] = true;
                $details['sdb'] = ' Salles de bain: ' . $sdbDemande . ' ≤ ' . $sdbBien;
            } elseif ($sdbDemande - 1 <= $sdbBien) {
                $score += 5;
                $criteres['sdb'] = 'partiel';
                $details['sdb'] = ' Salles de bain: ' . $sdbDemande . ' (tolérance -1)';
            } else {
                $score += 0;
                $criteres['sdb'] = false;
                $details['sdb'] = ' Salles de bain: ' . $sdbDemande . ' > ' . $sdbBien;
            }
        }

        // 8. Équipements (8 points bonus)
        $equipementsDemande = $this->equipements;
        $equipementsBien = $this->getEquipementsBien($bien);
        $equipementsCommuns = array_intersect($equipementsDemande, $equipementsBien);
        $equipementsScore = count($equipementsCommuns) > 0 ? min(8, count($equipementsCommuns) * 2) : 0;
        $score += $equipementsScore;
        $criteres['equipements'] = $equipementsScore > 0 ? true : false;
        $details['equipements'] = $equipementsScore > 0 ? ' ' . count($equipementsCommuns) . ' équipement(s) correspondant(s)' : '❌ Aucun équipement correspondant';

        // Score maximum 100
        $scoreTotal = min(100, $score);

        // Niveau de correspondance
        $niveau = $this->getNiveau($scoreTotal);

        return [
            'score' => $scoreTotal,
            'niveau' => $niveau,
            'criteres' => $criteres,
            'details' => $details
        ];
    }

    private function getEquipementsBien(BienImmobilier $bien): array
    {
        $equipements = [];
        if ($bien->parking_disponible) $equipements[] = 'Parking';
        if ($bien->est_meuble) $equipements[] = 'Meublé';
        return $equipements;
    }

    private function getNiveau(int $score): string
    {
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Bon';
        if ($score >= 40) return 'Moyen';
        if ($score >= 20) return 'Faible';
        return 'Minimal';
    }
    /**
 * Get the route key for the model.
 */
public function getRouteKeyName(): string
{
    return 'slug';
}
}
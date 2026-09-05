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
    
    protected $slugSource = 'description';

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

    public function getQuartierNameAttribute(): string
    {
        return $this->quartier ? $this->quartier->nom : $this->zone_recherchee;
    }

    public function getPropositionsCountAttribute()
    {
        return $this->propositions()->count();
    }

    /**
     * ✅ Budget formaté selon le type d'opération
     */
    public function getBudgetFormateAttribute(): string
    {
        if ($this->type_operation instanceof TypeOperationEnum && $this->type_operation->value === 'location') {
            return number_format($this->budget_maximum ?? 0, 0, ',', ' ') . ' F/mois';
        }
        return number_format($this->budget_maximum ?? 0, 0, ',', ' ') . ' F';
    }

    /**
     * ✅ Label du budget selon le type d'opération
     */
    public function getBudgetLabelAttribute(): string
    {
        if ($this->type_operation instanceof TypeOperationEnum && $this->type_operation->value === 'location') {
            return 'Loyer max / mois';
        }
        return "Budget d'achat";
    }

    /**
     * ✅ Vérifie si la date d'entrée est pertinente (seulement pour la location)
     */
    public function getAfficherDateEntreeAttribute(): bool
    {
        return $this->type_operation instanceof TypeOperationEnum && 
               $this->type_operation->value === 'location' && 
               !empty($this->date_entree_souhaitee);
    }

    /**
     * ✅ Récupère les critères spécifiques selon le type
     */
    public function getCriteresSpecifiquesAttribute(): array
    {
        $criteres = [];
        
        if ($this->type_operation instanceof TypeOperationEnum) {
            if ($this->type_operation->value === 'location') {
                $criteres = [
                    'type' => 'location',
                    'budget_label' => 'Loyer max / mois',
                    'budget' => number_format($this->budget_maximum ?? 0, 0, ',', ' ') . ' F/mois',
                    'date_entree' => $this->date_entree_souhaitee ? $this->date_entree_souhaitee->format('d/m/Y') : null,
                ];
            } else {
                $criteres = [
                    'type' => 'achat',
                    'budget_label' => "Budget d'achat",
                    'budget' => number_format($this->budget_maximum ?? 0, 0, ',', ' ') . ' F',
                    'surface' => $this->surface_minimum ? $this->surface_minimum . ' m²' : null,
                    'chambres' => $this->nombre_chambres,
                ];
            }
        }
        
        return $criteres;
    }

    // ==================== ✅ ÉQUIPEMENTS COMPLETS ====================

    /**
     * Récupère la liste complète des équipements demandés
     */
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

    /**
     * Vérifie si la demande a un équipement spécifique
     */
    public function hasEquipement(string $equipement): bool
    {
        return in_array($equipement, $this->equipements);
    }

    /**
     * Récupère le nombre d'équipements demandés
     */
    public function getEquipementsCountAttribute(): int
    {
        return count($this->equipements);
    }

    /**
     * Récupère les équipements avec leurs statuts (pour l'affichage)
     */
    public function getEquipementsWithStatusAttribute(): array
    {
        return [
            'parking' => ['label' => '🚗 Parking', 'value' => $this->parking ?? false],
            'meuble' => ['label' => '🛋️ Meublé', 'value' => $this->meuble ?? false],
            'climatisation' => ['label' => '❄️ Climatisation', 'value' => $this->climatisation ?? false],
            'balcon' => ['label' => '🌅 Balcon', 'value' => $this->balcon ?? false],
            'jardin' => ['label' => '🌿 Jardin', 'value' => $this->jardin ?? false],
            'piscine' => ['label' => '🏊 Piscine', 'value' => $this->piscine ?? false],
            'ascenseur' => ['label' => '🛗 Ascenseur', 'value' => $this->ascenseur ?? false],
            'securite' => ['label' => '🛡️ Sécurité 24h/24', 'value' => $this->securite ?? false],
        ];
    }

    // ==================== MÉTHODE DE MATCHING COMPLÈTE ====================

    /**
     * Calcule le score de matching entre une demande et un bien
     */
    public function calculerScore(BienImmobilier $bien): array
    {
        $score = 0;
        $details = [];
        $criteres = [];

        // === 1. TYPE D'OPÉRATION (OBLIGATOIRE - 20 points) ===
        $typeOperation = $this->type_operation instanceof \UnitEnum ? $this->type_operation->value : $this->type_operation;
        $typeContrat = $bien->type_contrat instanceof \UnitEnum ? $bien->type_contrat->value : $bien->type_contrat;
        
        if ($typeOperation === $typeContrat) {
            $score += 20;
            $criteres['type_operation'] = true;
            $details['type_operation'] = '✅ Type d\'opération: ' . $typeOperation;
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['❌ Type d\'opération ne correspond pas']];
        }

        // === 2. TYPE DE BIEN (OBLIGATOIRE - 20 points) ===
        $typeBien = $this->type_bien instanceof \UnitEnum ? $this->type_bien->value : $this->type_bien;
        $typeBienBien = $bien->type_bien instanceof \UnitEnum ? $bien->type_bien->value : $bien->type_bien;
        
        if ($typeBien === $typeBienBien) {
            $score += 20;
            $criteres['type_bien'] = true;
            $details['type_bien'] = '✅ Type de bien: ' . $typeBien;
        } else {
            return ['score' => 0, 'niveau' => 'Incompatible', 'details' => ['❌ Type de bien ne correspond pas']];
        }

        // === 3. ZONE GÉOGRAPHIQUE (20 points - OPTIONNEL) ===
        // ✅ Fonction pour normaliser les chaînes (supprimer les accents)
        $normaliserChaine = function($str) {
            $str = trim($str);
            $str = strtolower($str);
            // Supprimer les accents
            $unwanted_array = array(
                'Š'=>'s', 'š'=>'s', 'Ž'=>'z', 'ž'=>'z', 'À'=>'a', 'Á'=>'a', 'Â'=>'a', 'Ã'=>'a', 'Ä'=>'a', 'Å'=>'a', 'Æ'=>'a',
                'Ç'=>'c', 'È'=>'e', 'É'=>'e', 'Ê'=>'e', 'Ë'=>'e', 'Ì'=>'i', 'Í'=>'i', 'Î'=>'i', 'Ï'=>'i', 'Ñ'=>'n',
                'Ò'=>'o', 'Ó'=>'o', 'Ô'=>'o', 'Õ'=>'o', 'Ö'=>'o', 'Ø'=>'o', 'Ù'=>'u', 'Ú'=>'u', 'Û'=>'u', 'Ü'=>'u',
                'Ý'=>'y', 'Þ'=>'b', 'ß'=>'s', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a',
                'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o',
                'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u',
                'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y'
            );
            return strtr($str, $unwanted_array);
        };

        $zoneDemande = $normaliserChaine($this->zone_recherchee ?? '');
        $zoneBien = $normaliserChaine($bien->quartier ?? '');

        $zoneMatch = false;
        if (!empty($zoneDemande) && !empty($zoneBien)) {
            if ($zoneDemande === $zoneBien || 
                strpos($zoneBien, $zoneDemande) !== false || 
                strpos($zoneDemande, $zoneBien) !== false) {
                $zoneMatch = true;
            }
        }

        // ✅ La zone est maintenant OPTIONNELLE (pas bloquante)
        if ($zoneMatch) {
            $score += 20;
            $criteres['zone'] = true;
            $details['zone'] = '✅ Zone: ' . $this->zone_recherchee;
        } else {
            $score += 0;
            $criteres['zone'] = false;
            $details['zone'] = '⚠️ Zone: ' . $this->zone_recherchee . ' (hors zone de l\'agence)';
            // ✅ On ne bloque pas le matching
        }

        // === 4. BUDGET (15 points) ===
        $prixBien = floatval($bien->prix);
        $budgetMax = floatval($this->budget_maximum);
        
        if ($prixBien <= $budgetMax) {
            $score += 15;
            $criteres['budget'] = true;
            $details['budget'] = '✅ Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F ≤ ' . number_format($budgetMax, 0, ',', ' ') . ' F';
        } else {
            $score += 0;
            $criteres['budget'] = false;
            $details['budget'] = '❌ Budget: ' . number_format($prixBien, 0, ',', ' ') . ' F > ' . number_format($budgetMax, 0, ',', ' ') . ' F';
        }

        // === 5. CRITÈRES SPÉCIFIQUES SELON LE TYPE D'OPÉRATION ===

        if ($typeOperation === 'location') {
            // --- LOCATION ---

            // 5a. Date d'entrée (5 points)
            if ($this->date_entree_souhaitee) {
                $score += 5;
                $criteres['date_entree'] = true;
                $details['date_entree'] = '✅ Entrée: ' . $this->date_entree_souhaitee->format('d/m/Y');
            }

            // 5b. Meublé (5 points)
            if (isset($this->meuble) && $bien->est_meuble === (bool)$this->meuble) {
                $score += 5;
                $criteres['meuble'] = true;
                $details['meuble'] = $this->meuble ? '✅ Meublé' : '✅ Non meublé';
            } else {
                $criteres['meuble'] = false;
                $details['meuble'] = '❌ Meublé ne correspond pas';
            }

            // 5c. Surface (optionnelle - 5 points)
            if ($this->surface_minimum && $bien->surface >= $this->surface_minimum) {
                $score += 5;
                $criteres['surface'] = true;
                $details['surface'] = '✅ Surface: ' . $this->surface_minimum . ' m² ≤ ' . $bien->surface . ' m²';
            } else {
                $criteres['surface'] = 'non_applicable';
                $details['surface'] = '⏭️ Surface non applicable pour la location';
            }

        } else {
            // --- ACHAT ---

            // 5a. Surface (10 points)
            if ($this->surface_minimum && $bien->surface >= $this->surface_minimum) {
                $score += 10;
                $criteres['surface'] = true;
                $details['surface'] = '✅ Surface: ' . $this->surface_minimum . ' m² ≤ ' . $bien->surface . ' m²';
            } else {
                $criteres['surface'] = false;
                $details['surface'] = '❌ Surface: ' . $this->surface_minimum . ' m² > ' . $bien->surface . ' m²';
            }

            // 5b. Nombre de chambres (10 points)
            if ($this->nombre_chambres && $bien->nombre_chambres >= $this->nombre_chambres) {
                $score += 10;
                $criteres['chambres'] = true;
                $details['chambres'] = '✅ Chambres: ' . $this->nombre_chambres . ' ≤ ' . $bien->nombre_chambres;
            } else {
                $criteres['chambres'] = false;
                $details['chambres'] = '❌ Chambres: ' . $this->nombre_chambres . ' > ' . $bien->nombre_chambres;
            }

            // 5c. Salles de bain (5 points)
            if ($this->nombre_salles_bain && $bien->nombre_salles_bain >= $this->nombre_salles_bain) {
                $score += 5;
                $criteres['sdb'] = true;
                $details['sdb'] = '✅ SDB: ' . $this->nombre_salles_bain . ' ≤ ' . $bien->nombre_salles_bain;
            } else {
                $criteres['sdb'] = false;
                $details['sdb'] = '❌ SDB: ' . $this->nombre_salles_bain . ' > ' . $bien->nombre_salles_bain;
            }
        }

        // === 6. ✅ ÉQUIPEMENTS (10 points - OPTIONNELS) ===
        $equipementsDemande = $this->equipements;
        $equipementsBien = $bien->equipements;

        // Filtrer "Meublé" des équipements pour éviter le double comptage
        $equipementsDemande = array_filter($equipementsDemande, function($item) {
            return $item !== 'Meublé';
        });
        $equipementsBien = array_filter($equipementsBien, function($item) {
            return $item !== 'Meublé';
        });

        $equipementsCommuns = array_intersect($equipementsDemande, $equipementsBien);

        if (count($equipementsDemande) === 0) {
            // L'utilisateur n'a pas demandé d'équipements spécifiques
            $equipementsScore = 10;
            $details['equipements'] = '✅ Aucun équipement demandé (flexibilité totale)';
            $criteres['equipements'] = true;
        } elseif (count($equipementsCommuns) > 0) {
            // L'utilisateur a demandé des équipements ET le bien en a certains
            $equipementsScore = min(10, count($equipementsCommuns) * 2);
            $details['equipements'] = '✅ ' . count($equipementsCommuns) . ' équipement(s) correspondant(s): ' . implode(', ', $equipementsCommuns);
            $criteres['equipements'] = true;
        } else {
            // L'utilisateur a demandé des équipements MAIS le bien n'en a aucun
            $equipementsScore = 0;
            $equipementsManquants = implode(', ', $equipementsDemande);
            $details['equipements'] = '⚠️ Équipements demandés non disponibles: ' . $equipementsManquants;
            $criteres['equipements'] = false;
        }

        $score += $equipementsScore;

        // === SCORE FINAL ===
        $scoreTotal = min(100, $score);
        $niveau = $this->getNiveau($scoreTotal);

        return [
            'score' => $scoreTotal,
            'niveau' => $niveau,
            'criteres' => $criteres,
            'details' => $details
        ];
    }

    private function getNiveau(int $score): string
    {
        if ($score >= 80) return 'Excellent ';
        if ($score >= 60) return 'Bon ';
        if ($score >= 40) return 'Moyen ';
        if ($score >= 20) return 'Faible';
        return 'Minimal';
    }

    // ==================== ROUTE KEY ====================
    
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    // ==================== MÉTHODES DE NETTOYAGE ====================

/**
 * Supprime automatiquement les demandes expirées (plus de 30 jours)
 */
public static function supprimerDemandesExpirees(): int
{
    $count = 0;
    
    // Récupérer les demandes en attente de plus de 30 jours
    $demandesExpirees = self::where('statut', StatutDemandeEnum::EN_ATTENTE)
        ->where('created_at', '<', now()->subDays(30))
        ->get();
    
    foreach ($demandesExpirees as $demande) {
        // Supprimer les propositions liées
        $demande->propositions()->delete();
        // Supprimer la demande
        $demande->delete();
        $count++;
    }
    
    return $count;
}

/**
 * Vérifie si une demande est expirée
 */
public function estExpiree(): bool
{
    return $this->statut->value === StatutDemandeEnum::EN_ATTENTE->value 
        && $this->created_at->diffInDays(now()) > 30;
}
}
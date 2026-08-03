<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\StatutPropositionEnum;

class Agence extends Model
{
    use HasFactory;

    protected $table = 'agences';

    protected $fillable = [
        'user_id',
        'nom_agence',
        'adresse',
        'quartier',
        'quartier_id',
        'description',
        'logo',
        'zones_intervention',
        'statut_validation',
    ];

    protected $casts = [
        'statut_validation' => 'boolean',
        'zones_intervention' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentAgence::class);
    }

    public function biens(): HasMany
    {
        return $this->hasMany(BienImmobilier::class);
    }

    public function abonnements(): HasMany
    {
        return $this->hasMany(Abonnement::class);
    }

    public function propositions(): HasMany
    {
        return $this->hasMany(Proposition::class);
    }

    public function rendezVous(): HasMany
    {
        return $this->hasMany(RendezVous::class);
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class);
    }

    public function quartier(): BelongsTo
    {
        return $this->belongsTo(Quartier::class);
    }

    /**
     * Récupère l'abonnement actif de l'agence
     */
    public function abonnementActif(): HasOne
    {
        return $this->hasOne(Abonnement::class)
            ->where('statut', true)
            ->where('date_fin', '>', now());
    }

    /**
     * Vérifie si l'agence a un abonnement actif
     */
    public function aAbonnementActif(): bool
    {
        return $this->abonnementActif()->exists();
    }

    /**
     * Récupère l'abonnement actif ou null
     */
    public function getAbonnementActifAttribute()
    {
        return $this->abonnementActif()->first();
    }

    /**
     * Récupère le nombre d'offres envoyées ce mois-ci
     */
    public function getOffresEnvoyeesMoisAttribute(): int
    {
        return $this->propositions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Récupère le nombre d'offres restantes
     */
    /**
 * Récupère le nombre d'offres restantes
 */
public function getOffresRestantesAttribute(): int
{
    $abonnement = $this->abonnementActif;
    if (!$abonnement) {
        return 0;
    }

    $limite = $abonnement->formule->limiteBiens();
    $utilisees = $this->offresEnvoyeesMois;
    
    // Si la limite est PHP_INT_MAX (illimité), retourner un grand nombre ou "∞"
    if ($limite === PHP_INT_MAX) {
        return 999999; // Ou n'importe quel grand nombre
    }
    
    return max(0, $limite - $utilisees);
}

    /**
     * Vérifie si l'agence peut encore envoyer des offres
     */
   /**
 * Vérifie si l'agence peut encore envoyer des offres
 */
public function peutEnvoyerOffres(): bool
{
    if (!$this->aAbonnementActif()) {
        return false;
    }

    $abonnement = $this->abonnementActif;
    $limite = $abonnement->formule->limiteBiens();
    
    // Si la limite est PHP_INT_MAX (illimité), toujours autorisé
    if ($limite === PHP_INT_MAX) {
        return true;
    }
    
    return $this->offresRestantes > 0;
}

    /**
     * Récupère le pourcentage d'offres utilisées
     */
    public function getPourcentageOffresAttribute(): float
    {
        $abonnement = $this->abonnementActif;
        if (!$abonnement) {
            return 0;
        }

        $limite = $abonnement->formule->limiteBiens();
        if ($limite === PHP_INT_MAX) {
            return 0; // Illimité
        }

        $utilisees = $this->offresEnvoyeesMois;
        return round(($utilisees / $limite) * 100);
    }

    public function getNoteMoyenneAttribute(): float
    {
        return $this->evaluations()->avg('note') ?? 0;
    }

    public function estValidee(): bool
    {
        return $this->statut_validation;
    }

    /**
     * Récupère toutes les zones d'intervention de l'agence
     * (inclut le quartier principal + zones personnalisées)
     */
    public function getToutesZonesAttribute(): array
    {
        $zones = [];
        
        // Ajouter le quartier principal
        if ($this->quartier) {
            $zones[] = $this->quartier;
        }
        
        // Ajouter les zones d'intervention personnalisées
        if ($this->zones_intervention && is_array($this->zones_intervention)) {
            $zones = array_merge($zones, $this->zones_intervention);
        }
        
        return array_unique($zones);
    }

    /**
     * Vérifie si l'agence intervient dans une zone donnée
     */
    public function intervientDansZone(string $zone): bool
    {
        return in_array($zone, $this->toutesZones);
    }
}
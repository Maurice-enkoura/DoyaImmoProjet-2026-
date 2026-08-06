<?php
// app/Models/Agence.php

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
        'statut_validation',
        'bloque',
    ];

    protected $casts = [
        'statut_validation' => 'boolean',
        'bloque' => 'boolean',
        'zones_intervention' => 'array',
    ];

    // ==================== RELATIONS ====================
    
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

    // ==================== RELATION CRÉNEAUX ====================
    // AJOUTER CETTE RELATION ICI
    public function creneaux(): HasMany
    {
        return $this->hasMany(CreneauRendezVous::class, 'agence_id');
    }

    // ==================== ABONNEMENT ====================

    public function abonnementActif(): HasOne
    {
        return $this->hasOne(Abonnement::class)
            ->where('statut', true)
            ->where('date_fin', '>', now());
    }

    public function aAbonnementActif(): bool
    {
        return $this->abonnementActif()->exists();
    }

    public function getAbonnementActifAttribute()
    {
        return $this->abonnementActif()->first();
    }

    // ==================== OFFRES ====================

    public function getOffresEnvoyeesMoisAttribute(): int
    {
        return $this->propositions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    public function getOffresRestantesAttribute(): int
    {
        $abonnement = $this->abonnementActif;
        if (!$abonnement) {
            return 0;
        }

        $limite = $abonnement->formule->limiteBiens();
        $utilisees = $this->offresEnvoyeesMois;
        
        if ($limite === PHP_INT_MAX) {
            return 999999;
        }
        
        return max(0, $limite - $utilisees);
    }

    public function peutEnvoyerOffres(): bool
    {
        if (!$this->aAbonnementActif()) {
            return false;
        }

        $abonnement = $this->abonnementActif;
        $limite = $abonnement->formule->limiteBiens();
        
        if ($limite === PHP_INT_MAX) {
            return true;
        }
        
        return $this->offresRestantes > 0;
    }

    public function getPourcentageOffresAttribute(): float
    {
        $abonnement = $this->abonnementActif;
        if (!$abonnement) {
            return 0;
        }

        $limite = $abonnement->formule->limiteBiens();
        if ($limite === PHP_INT_MAX) {
            return 0;
        }

        $utilisees = $this->offresEnvoyeesMois;
        return round(($utilisees / $limite) * 100);
    }

    // ==================== ÉVALUATIONS ====================

    public function getNoteMoyenneAttribute(): float
    {
        return $this->evaluations()->avg('note') ?? 0;
    }

    public function estValidee(): bool
    {
        return $this->statut_validation;
    }

    // ==================== ZONES ====================

    public function getToutesZonesAttribute(): array
    {
        $zones = [];
        
        if ($this->quartier) {
            $zones[] = $this->quartier;
        }
        
        if ($this->zones_intervention && is_array($this->zones_intervention)) {
            $zones = array_merge($zones, $this->zones_intervention);
        }
        
        return array_unique($zones);
    }

    public function intervientDansZone(string $zone): bool
    {
        return in_array($zone, $this->toutesZones);
    }
}
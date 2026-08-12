<?php
// app/Models/Agence.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\StatutPropositionEnum;
use Carbon\Carbon;

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
        // ✅ Ajouter les nouveaux champs
        'est_refusee',
        'motif_refus',
        'date_refus',
    ];

    protected $casts = [
        'statut_validation' => 'boolean',
        'bloque' => 'boolean',
        'est_refusee' => 'boolean',
        'zones_intervention' => 'array',
        'date_refus' => 'datetime', // ✅ Convertir en objet Carbon
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

    // ==================== ✅ NOUVEAUX ACCESSORS ====================

    /**
     * Accesseur pour le statut de l'agence (label)
     */
    public function getStatutLabelAttribute(): string
    {
        if ($this->est_refusee) {
            return 'Refusée';
        }
        if ($this->bloque) {
            return 'Bloquée';
        }
        if ($this->statut_validation) {
            return 'Validée';
        }
        return 'En attente';
    }

    /**
     * Accesseur pour la classe CSS du statut
     */
    public function getStatutClassAttribute(): string
    {
        if ($this->est_refusee || $this->bloque) {
            return 'status-refusee';
        }
        if ($this->statut_validation) {
            return 'status-active';
        }
        return 'status-en_attente';
    }

    /**
     * Accesseur pour la date de refus formatée
     */
    public function getDateRefusFormateeAttribute(): string
    {
        if (empty($this->date_refus)) {
            return '-';
        }
        
        try {
            // Si c'est déjà un objet Carbon
            if ($this->date_refus instanceof Carbon) {
                return $this->date_refus->format('d/m/Y');
            }
            // Si c'est une chaîne
            return Carbon::parse($this->date_refus)->format('d/m/Y');
        } catch (\Exception $e) {
            return (string) $this->date_refus;
        }
    }

    /**
     * Accesseur pour savoir si l'agence est refusée
     */
    public function getEstRefuseeAttribute(): bool
    {
        return $this->attributes['est_refusee'] ?? false;
    }

    /**
     * Accesseur pour savoir si l'agence est en attente
     */
    public function getEstEnAttenteAttribute(): bool
    {
        return !$this->statut_validation && !$this->est_refusee && !$this->bloque;
    }

    /**
     * Accesseur pour savoir si l'agence est validée
     */
    public function getEstValideeAttribute(): bool
    {
        return $this->statut_validation && !$this->bloque;
    }
}
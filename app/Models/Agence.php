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
    use \App\Traits\Sluggable;

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
        'slug',
        'est_refusee',
        'motif_refus',
        'date_refus',
        'zones_intervention',
    ];
    
    protected $slugSource = 'nom';

    protected $casts = [
        'statut_validation' => 'boolean',
        'bloque' => 'boolean',
        'est_refusee' => 'boolean',
        'zones_intervention' => 'array',
        'date_refus' => 'datetime', 
        'slug' => 'string',
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

    // ==================== ÉVALUATIONS & RÉPUTATION ====================

    /**
     * Calcule la note moyenne de l'agence
     */
    public function getNoteMoyenneAttribute(): float
    {
        return $this->evaluations()->avg('note') ?? 0;
    }

    /**
     * Calcule le nombre total d'évaluations
     */
    public function getNombreEvaluationsAttribute(): int
    {
        return $this->evaluations()->count();
    }

    /**
     * Récupère la répartition des notes (1 à 5 étoiles)
     */
    public function getRepartitionNotesAttribute(): array
    {
        $repartition = [];
        for ($i = 1; $i <= 5; $i++) {
            $repartition[$i] = $this->evaluations()
                ->where('note', $i)
                ->count();
        }
        return $repartition;
    }

    /**
     * Récupère le pourcentage de chaque note
     */
    public function getPourcentageNotesAttribute(): array
    {
        $total = $this->nombreEvaluations;
        if ($total === 0) {
            return [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0];
        }

        $pourcentage = [];
        foreach ($this->repartitionNotes as $note => $count) {
            $pourcentage[$note] = round(($count / $total) * 100);
        }
        return $pourcentage;
    }

    /**
     * Récupère les évaluations récentes (limité à 5)
     */
    public function getEvaluationsRecentesAttribute()
    {
        return $this->evaluations()
            ->with('particulier.user')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
    }

    /**
     * Vérifie si l'agence a des évaluations
     */
    public function getAEvaluationsAttribute(): bool
    {
        return $this->evaluations()->exists();
    }

    /**
     * Récupère la note moyenne formatée (ex: 4.5/5)
     */
    public function getNoteMoyenneFormateeAttribute(): string
    {
        $note = $this->note_moyenne;
        return number_format($note, 1) . ' / 5';
    }

    /**
     * Récupère le nombre d'étoiles pleines pour l'affichage
     */
    public function getEtoilesPleinesAttribute(): int
    {
        return (int) floor($this->note_moyenne);
    }

    /**
     * Récupère le nombre d'étoiles vides pour l'affichage
     */
    public function getEtoilesVidesAttribute(): int
    {
        return 5 - $this->etoiles_pleines;
    }

    /**
     * Récupère la note moyenne arrondie
     */
    public function getNoteArrondieAttribute(): int
    {
        return (int) round($this->note_moyenne);
    }

    // ==================== STATUT ====================

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

    // ==================== ACCESSORS STATUT ====================

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
            if ($this->date_refus instanceof Carbon) {
                return $this->date_refus->format('d/m/Y');
            }
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

    // ==================== ROUTE KEY ====================
    
    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
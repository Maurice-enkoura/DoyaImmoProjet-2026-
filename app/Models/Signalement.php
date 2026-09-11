<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Enums\StatutSignalementEnum;
use App\Enums\MotifSignalementEnum;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = [
        'particulier_id',
        'agence_id',        // ✅ Ajouté
        'signalable_type',
        'signalable_id',
        'motif',
        'description',
        'statut',
        'date_signalement',
        'date_traitement',
        'commentaire_admin',
        // 'sanction' => ❌ Supprimé car n'existe pas dans la table
    ];

    protected $casts = [
        'date_signalement' => 'datetime',
        'date_traitement' => 'datetime',
        'statut' => StatutSignalementEnum::class,
        // 'motif' => MotifSignalementEnum::class,  // Optionnel si vous voulez caster le motif
    ];

    // ==================== RELATIONS ====================
    
    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function signalable(): MorphTo
    {
        return $this->morphTo();
    }

    // ==================== ACCESSORS ====================
    
    /**
     * Récupère le libellé du motif
     */
    public function getMotifLabelAttribute(): string
    {
        $motifs = [
            'fraude' => 'Fraude',
            'arnaque' => 'Arnaque',
            'contenu_inapproprie' => 'Contenu inapproprié',
            'fausse_annonce' => 'Fausse annonce',
            'comportement_inapproprié' => 'Comportement inapproprié',
            'autre' => 'Autre',
        ];
        return $motifs[$this->motif] ?? $this->motif;
    }

    /**
     * Récupère le libellé du statut
     */
    public function getStatutLabelAttribute(): string
    {
        if (is_object($this->statut)) {
            return $this->statut->label();
        }
        
        $statuts = [
            'en_attente' => 'En attente',
            'traite' => 'Traité',
            'rejete' => 'Rejeté',
        ];
        return $statuts[$this->statut] ?? $this->statut;
    }

    /**
     * Récupère la classe CSS du statut
     */
    public function getStatutClassAttribute(): string
    {
        $statuts = [
            'en_attente' => 'status-en_attente',
            'traite' => 'status-traite',
            'rejete' => 'status-rejete',
        ];
        return $statuts[$this->statut] ?? 'status-en_attente';
    }

    // ==================== SCOPES ====================
    
    public function scopeEnAttente($query)
    {
        return $query->where('statut', 'en_attente');
    }

    public function scopeTraites($query)
    {
        return $query->where('statut', 'traite');
    }

    public function scopeRejetes($query)
    {
        return $query->where('statut', 'rejete');
    }

    // ==================== BOOLEANS ====================
    
    public function estEnAttente(): bool
    {
        $statut = is_object($this->statut) ? $this->statut->value : $this->statut;
        return $statut === 'en_attente';
    }

    public function estTraite(): bool
    {
        $statut = is_object($this->statut) ? $this->statut->value : $this->statut;
        return $statut === 'traite';
    }

    public function estRejete(): bool
    {
        $statut = is_object($this->statut) ? $this->statut->value : $this->statut;
        return $statut === 'rejete';
    }
}
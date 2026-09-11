<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\TypeDocumentEnum;
use App\Enums\StatutDocumentEnum;

class DocumentAgence extends Model
{
    use HasFactory;

    protected $table = 'document_agences';

    protected $fillable = [
        'agence_id',
        'type_document',
        'nom_fichier',
        'statut_validation',
        'valide_par',
        'date_validation',
        'commentaire',
    ];

    // ✅ Cast correct vers les Enums
    protected $casts = [
        'type_document' => TypeDocumentEnum::class,
        'statut_validation' => StatutDocumentEnum::class,
        'date_validation' => 'datetime',
    ];

    // ==================== RELATIONS ====================

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(Administrateur::class, 'valide_par');
    }

    // ==================== SCOPES ====================

    public function scopeEnAttente($query)
    {
        return $query->where('statut_validation', StatutDocumentEnum::EN_ATTENTE->value);
    }

    public function scopeValides($query)
    {
        return $query->where('statut_validation', StatutDocumentEnum::VALIDE->value);
    }

    public function scopeRejetes($query)
    {
        return $query->where('statut_validation', StatutDocumentEnum::REJETE->value);
    }

    // ==================== ACCESSORS ====================

    /**
     * Accesseur pour le libellé du type de document
     */
    public function getTypeDocumentLabelAttribute(): string
    {
        return $this->type_document->label() ?? ucfirst($this->type_document->value);
    }

    /**
     * Accesseur pour la valeur du type de document
     */
    public function getTypeDocumentValueAttribute(): string
    {
        return $this->type_document->value ?? 'unknown';
    }

    /**
     * Accesseur pour le libellé du statut de validation
     */
    public function getStatutValidationLabelAttribute(): string
    {
        return $this->statut_validation->label() ?? ucfirst($this->statut_validation->value);
    }

    /**
     * Accesseur pour la valeur du statut de validation
     */
    public function getStatutValidationValueAttribute(): string
    {
        return $this->statut_validation->value ?? 'en_attente';
    }

    /**
     * Accesseur pour savoir si le document est valide
     */
    public function getEstValideAttribute(): bool
    {
        return $this->statut_validation === StatutDocumentEnum::VALIDE;
    }

    /**
     * Accesseur pour savoir si le document est en attente
     */
    public function getEstEnAttenteAttribute(): bool
    {
        return $this->statut_validation === StatutDocumentEnum::EN_ATTENTE;
    }

    /**
     * Accesseur pour savoir si le document est rejeté
     */
    public function getEstRejeteAttribute(): bool
    {
        return $this->statut_validation === StatutDocumentEnum::REJETE;
    }

    /**
     * Accesseur pour le chemin complet du fichier
     */
    public function getFichierUrlAttribute(): string
    {
        return asset('storage/' . $this->nom_fichier);
    }

    /**
     * Accesseur pour la classe CSS du statut
     */
    public function getStatutClassAttribute(): string
    {
        return $this->statut_validation->badge() ?? 'default';
    }
}
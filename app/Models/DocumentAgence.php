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
        return $query->where('statut_validation', StatutDocumentEnum::EN_ATTENTE);
    }

    public function scopeValides($query)
    {
        return $query->where('statut_validation', StatutDocumentEnum::VALIDE);
    }

    public function scopeRejetes($query)
    {
        return $query->where('statut_validation', StatutDocumentEnum::REJETE);
    }

    // ==================== ACCESSORS ====================

    /**
     * Accesseur pour le libellé du type de document
     */
    public function getTypeDocumentLabelAttribute()
    {
        if (is_object($this->type_document) && method_exists($this->type_document, 'label')) {
            return $this->type_document->label();
        }
        
        // Définir les libellés des types de documents
        $labels = [
            'rccm' => 'Registre de Commerce (RCCM)',
            'ninea' => 'NINEA',
            'piece_identite' => 'Pièce d\'identité',
            'logo' => 'Logo',
        ];
        
        // Récupérer la valeur du type
        $value = $this->getTypeDocumentValueAttribute();
        
        return $labels[$value] ?? ucfirst($value);
    }

    /**
     * Accesseur pour la valeur du type de document
     */
    public function getTypeDocumentValueAttribute()
    {
        if (is_object($this->type_document) && method_exists($this->type_document, 'value')) {
            return $this->type_document->value;
        }
        // Si c'est déjà une chaîne
        if (is_string($this->type_document)) {
            return $this->type_document;
        }
        // Fallback
        return 'unknown';
    }

    /**
     * Accesseur pour le libellé du statut de validation
     */
    public function getStatutValidationLabelAttribute()
    {
        if (is_object($this->statut_validation) && method_exists($this->statut_validation, 'label')) {
            return $this->statut_validation->label();
        }
        
        $labels = [
            'en_attente' => 'En attente',
            'valide' => 'Validé',
            'rejete' => 'Rejeté',
        ];
        
        $value = $this->getStatutValidationValueAttribute();
        
        return $labels[$value] ?? ucfirst($value);
    }

    /**
     * Accesseur pour la valeur du statut de validation
     */
    public function getStatutValidationValueAttribute()
    {
        if (is_object($this->statut_validation) && method_exists($this->statut_validation, 'value')) {
            return $this->statut_validation->value;
        }
        // Si c'est déjà une chaîne
        if (is_string($this->statut_validation)) {
            return $this->statut_validation;
        }
        // Fallback
        return 'en_attente';
    }

    /**
     * Accesseur pour savoir si le document est valide
     */
    public function getEstValideAttribute(): bool
    {
        $value = $this->getStatutValidationValueAttribute();
        return $value === 'valide';
    }

    /**
     * Accesseur pour savoir si le document est en attente
     */
    public function getEstEnAttenteAttribute(): bool
    {
        $value = $this->getStatutValidationValueAttribute();
        return $value === 'en_attente';
    }

    /**
     * Accesseur pour savoir si le document est rejeté
     */
    public function getEstRejeteAttribute(): bool
    {
        $value = $this->getStatutValidationValueAttribute();
        return $value === 'rejete';
    }

    /**
     * Accesseur pour le chemin complet du fichier
     */
    public function getFichierUrlAttribute(): string
    {
        return asset('storage/' . $this->nom_fichier);
    }
}
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

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function validePar(): BelongsTo
    {
        return $this->belongsTo(Administrateur::class, 'valide_par');
    }

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
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ValidationAgence extends Model
{
    use HasFactory;

    protected $table = 'validations_agences';

    protected $fillable = [
        'agence_id',
        'administrateur_id',
        'statut',
        'commentaire',
        'date_validation',
    ];

    protected $casts = [
        'date_validation' => 'datetime',
        'statut' => 'boolean',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function administrateur(): BelongsTo
    {
        return $this->belongsTo(Administrateur::class);
    }
}
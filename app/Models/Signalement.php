<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Enums\StatutSignalementEnum;

class Signalement extends Model
{
    use HasFactory;

    protected $fillable = [
        'particulier_id',
        'agence_id',
        'signalable_type',
        'signalable_id',
        'motif',
        'description',
        'statut',
        'date_signalement',
        'date_traitement',
        'commentaire_admin',
        'sanction',
    ];

    protected $casts = [
        'date_signalement' => 'datetime',
        'date_traitement' => 'datetime',
        'statut' => StatutSignalementEnum::class,
    ];

    // Relations
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

    // Accesseurs
    public function getMotifLabelAttribute()
    {
        $motifs = [
            'arnaque' => 'Arnaque',
            'contenu_inapproprie' => 'Contenu inapproprié',
            'fausse_annonce' => 'Fausse annonce',
            'comportement_inapproprié' => 'Comportement inapproprié',
            'autre' => 'Autre',
        ];
        return $motifs[$this->motif] ?? $this->motif;
    }
}
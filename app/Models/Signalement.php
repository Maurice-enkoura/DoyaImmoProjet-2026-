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

    protected $table = 'signalements';

    protected $fillable = [
        'particulier_id',
        'signalable_id',
        'signalable_type',
        'motif',
        'description',
        'statut',
        'date_signalement',
        'date_traitement',
        'commentaire_admin',
    ];

    protected $casts = [
        'statut' => StatutSignalementEnum::class,
        'motif' => MotifSignalementEnum::class,
        'date_signalement' => 'datetime',
        'date_traitement' => 'datetime',
    ];

    public function particulier(): BelongsTo
    {
        return $this->belongsTo(Particulier::class);
    }

    public function signalable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopeEnAttente($query)
    {
        return $query->where('statut', StatutSignalementEnum::EN_ATTENTE);
    }

    public function scopeTraites($query)
    {
        return $query->where('statut', StatutSignalementEnum::TRAITE);
    }

    public function scopeRejetes($query)
    {
        return $query->where('statut', StatutSignalementEnum::REJETE);
    }
}
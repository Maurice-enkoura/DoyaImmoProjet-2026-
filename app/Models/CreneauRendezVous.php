<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CreneauRendezVous extends Model
{
    use HasFactory;

    protected $table = 'creneaux_rendez_vous';

    protected $fillable = [
        'agence_id',
        'date',
        'heure_debut',
        'heure_fin',
        'est_disponible',
    ];

    protected $casts = [
        'date' => 'date',
        'est_disponible' => 'boolean',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function estDisponible(): bool
    {
        return $this->est_disponible && $this->date >= Carbon::today();
    }

    public static function getDisponibles(Agence $agence, string $date)
    {
        return self::where('agence_id', $agence->id)
            ->where('date', $date)
            ->where('est_disponible', true)
            ->orderBy('heure_debut')
            ->get();
    }
}
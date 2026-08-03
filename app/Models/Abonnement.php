<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Enums\FormuleAbonnementEnum;

class Abonnement extends Model
{
    use HasFactory;

    protected $table = 'abonnements';

    protected $fillable = [
        'agence_id',
        'formule',
        'montant',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'formule' => FormuleAbonnementEnum::class,
        'montant' => 'decimal:2',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
        'statut' => 'boolean',
    ];

    public function agence(): BelongsTo
    {
        return $this->belongsTo(Agence::class);
    }

    public function scopeActifs($query)
    {
        return $query->where('statut', true)
            ->where('date_fin', '>', now());
    }

    public function scopeExpirant($query, int $days = 7)
    {
        return $query->where('statut', true)
            ->whereBetween('date_fin', [now(), now()->addDays($days)]);
    }

    public function estActif(): bool
    {
        return $this->statut && $this->date_fin > now();
    }

    public function estExpirant(int $days = 7): bool
    {
        return $this->statut && $this->date_fin->between(now(), now()->addDays($days));
    }
}
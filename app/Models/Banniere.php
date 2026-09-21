<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banniere extends Model
{
    use HasFactory;

    protected $fillable = [
        'titre',
        'sous_titre',
        'image',
        'lien',
        'ordre',
        'est_actif',
        'position_texte',
        'date_debut',
        'date_fin',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
        'date_debut' => 'datetime',
        'date_fin' => 'datetime',
    ];

    // ==================== SCOPES ====================

    public function scopeActifs($query)
    {
        return $query->where('est_actif', true)
            ->where(function ($q) {
                $q->whereNull('date_debut')
                  ->orWhere('date_debut', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('date_fin')
                  ->orWhere('date_fin', '>=', now());
            });
    }

    // ==================== ACCESSORS ====================

    public function getImageUrlAttribute(): ?string
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }

    public function getStatutReelAttribute(): string
    {
        if (!$this->est_actif) {
            return 'Inactif';
        }

        $now = now();

        if ($this->date_debut && $this->date_debut > $now) {
            return 'Programmée';
        }

        if ($this->date_fin && $this->date_fin < $now) {
            return 'Expirée';
        }

        return 'Active';
    }

    public function getStatutClassAttribute(): string
    {
        return match ($this->statut_reel) {
            'Active' => 'status-active',
            'Programmée' => 'status-en_attente',
            'Expirée' => 'status-inactif',
            'Inactif' => 'status-inactif',
            default => 'status-inactif',
        };
    }

    public function getJoursRestantsAttribute(): ?int
    {
        if (!$this->date_fin || $this->date_fin < now()) {
            return null;
        }
        return now()->diffInDays($this->date_fin, false);
    }

    public function getEstActiveAttribute(): bool
    {
        return $this->statut_reel === 'Active';
    }

    // ==================== MÉTHODES MÉTIER ====================

    public function necessiteReprogrammation(): bool
    {
        $now = now();

        if ($this->date_fin && $this->date_fin < $now) {
            return true;
        }

        if ($this->date_debut && $this->date_debut > $now) {
            return true;
        }

        return false;
    }
}
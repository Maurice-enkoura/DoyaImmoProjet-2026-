<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Quartier extends Model
{
    use HasFactory;

    protected $table = 'quartiers';

    protected $fillable = [
        'nom',
        'ville',
        'est_actif',
    ];

    protected $casts = [
        'est_actif' => 'boolean',
    ];

    public function demandes(): HasMany
    {
        return $this->hasMany(DemandeImmobiliere::class);
    }

    public function biens(): HasMany
    {
        return $this->hasMany(BienImmobilier::class);
    }

    public function agences(): HasMany
    {
        return $this->hasMany(Agence::class);
    }

    public function particuliers(): HasMany
    {
        return $this->hasMany(Particulier::class);
    }

    public function scopeActif($query)
    {
        return $query->where('est_actif', true);
    }

    public function scopeInactif($query)
    {
        return $query->where('est_actif', false);
    }

    public function scopeParVille($query, string $ville)
    {
        return $query->where('ville', $ville);
    }

    public function getFullNameAttribute(): string
    {
        return $this->nom . ' (' . $this->ville . ')';
    }

    public function getStatusLabelAttribute(): string
    {
        return $this->est_actif ? 'Actif' : 'Inactif';
    }

    public function getDemandesCountAttribute()
{
    return $this->demandes()->where('statut', 'en_attente')->count();
}
}
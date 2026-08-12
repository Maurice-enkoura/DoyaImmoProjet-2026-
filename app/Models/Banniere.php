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

    public function getImageUrlAttribute()
    {
        if ($this->image) {
            return asset('storage/' . $this->image);
        }
        return null;
    }
}
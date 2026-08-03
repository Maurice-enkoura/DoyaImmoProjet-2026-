<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Enums\RoleEnum;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'telephone',
        'mot_de_passe',
        'role',
    ];

    protected $hidden = [
        'mot_de_passe',
        'remember_token',
    ];

    protected $casts = [
        'role' => RoleEnum::class,
        'email_verified_at' => 'datetime',
    ];

    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    // Méthode pour obtenir le rôle en string (utile pour le middleware)
    public function getRoleAttribute($value)
    {
        if (is_string($value)) {
            return $value;
        }
        return $value instanceof RoleEnum ? $value->value : $value;
    }

    public function administrateur(): HasOne
    {
        return $this->hasOne(Administrateur::class);
    }

    public function particulier(): HasOne
    {
        return $this->hasOne(Particulier::class);
    }

    public function agence(): HasOne
    {
        return $this->hasOne(Agence::class);
    }

    public function isAdmin(): bool
    {
        return $this->role === RoleEnum::ADMIN || $this->role === 'admin';
    }

    public function isParticulier(): bool
    {
        return $this->role === RoleEnum::PARTICULIER || $this->role === 'particulier';
    }

    public function isAgence(): bool
    {
        return $this->role === RoleEnum::AGENCE || $this->role === 'agence';
    }

    public function getFullNameAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}
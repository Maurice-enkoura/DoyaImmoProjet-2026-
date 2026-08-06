<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use App\Enums\RoleEnum;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CreneauRendezVous;

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

    // ==================== RELATIONS ====================
    
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

    // ==================== RÔLES ====================
    
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

    // ==================== ACCESSORS ====================
    
    public function getFullNameAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    // ==================== NOTIFICATIONS PERSONNALISÉES ====================
    
    /**
     * Envoie une notification en base de données
     * Surcharge la méthode native de Laravel pour garantir l'enregistrement
     */
    public function notify($notification)
    {
        // Si c'est une string, on instancie la classe
        if (is_string($notification)) {
            $notification = app($notification);
        }
        
        // Vérifier si la notification a une méthode toDatabase
        if (method_exists($notification, 'toDatabase')) {
            $data = $notification->toDatabase($this);
            
            // Créer la notification en base de données
            DatabaseNotification::create([
                'id' => (string) Str::uuid(),
                'type' => get_class($notification),
                'notifiable_type' => get_class($this),
                'notifiable_id' => $this->id,
                'data' => $data,
                'read_at' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Envoie une notification à plusieurs utilisateurs
     */
    public static function notifyMany($users, $notification)
    {
        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

    /**
     * Récupère les notifications non lues avec leurs données formatées
     */
    public function getUnreadNotificationsFormatted()
    {
        return $this->unreadNotifications()->get()->map(function ($notification) {
            return [
                'id' => $notification->id,
                'title' => $notification->data['title'] ?? 'Notification',
                'message' => $notification->data['message'] ?? '',
                'type' => $notification->data['type'] ?? 'info',
                'icon' => $notification->data['icon'] ?? 'fa-regular fa-bell',
                'link' => $notification->data['link'] ?? null,
                'created_at' => $notification->created_at,
                'created_at_human' => $notification->created_at->diffForHumans(),
            ];
        });
    }

    /**
     * Récupère le nombre de notifications non lues
     */
    public function getUnreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }
    public function creneaux(): HasMany
{
    return $this->hasMany(CreneauRendezVous::class);
}
}
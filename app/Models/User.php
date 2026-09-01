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
use Illuminate\Support\Facades\Mail;

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

    // ✅ IMPORTANT : Indiquer à Laravel la colonne utilisée pour le mot de passe
    public function getAuthPassword()
    {
        return $this->mot_de_passe;
    }

    // ✅ Méthode pour définir le mot de passe (utile pour les mutations)
    public function setMotDePasseAttribute($value)
    {
        $this->attributes['mot_de_passe'] = $value;
    }

    // ✅ Méthode pour Laravel (alias)
    public function getPasswordAttribute()
    {
        return $this->mot_de_passe;
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

    // ==================== ACCESSORS ====================
    
    public function getFullNameAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }

    public function getRoleAttribute($value)
    {
        if (is_string($value)) {
            return $value;
        }
        return $value instanceof RoleEnum ? $value->value : $value;
    }

    // ==================== NOTIFICATIONS ====================
    
public function notify($notification)
{
    if (is_string($notification)) {
        $notification = app($notification);
    }

    // 1️⃣ Envoyer l'email si la notification a une méthode toMail
    if (method_exists($notification, 'toMail')) {
        try {
            $mail = $notification->toMail($this);
            
            // Récupérer les variables AVANT la closure
            $userEmail = $this->email;
            $subject = $mail->subject;
            
            // ✅ Récupérer la vue depuis le MailMessage en inspectant la notification
            $view = $this->getViewFromNotification($notification);
            
            // ✅ Récupérer les données via réflexion
            $viewData = $this->extractNotificationData($notification);
            
            // ✅ Ajouter le notifiable (l'utilisateur courant)
            $viewData['notifiable'] = $this;
            
            // Envoyer l'email
            Mail::send($view, $viewData, function ($message) use ($userEmail, $subject) {
                $message->to($userEmail)
                        ->subject($subject);
            });
        } catch (\Exception $e) {
            \Log::error('Erreur envoi email: ' . $e->getMessage());
        }
    }

    // 2️⃣ Enregistrer en base de données si la notification a une méthode toDatabase
    if (method_exists($notification, 'toDatabase')) {
        $data = $notification->toDatabase($this);
        
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
 * Récupère la vue depuis la notification
 */
protected function getViewFromNotification($notification): string
{
    // Utiliser la réflexion pour inspecter la notification
    $reflection = new \ReflectionObject($notification);
    
    // Vérifier si la notification a une méthode toMail qui utilise markdown
    if (method_exists($notification, 'toMail')) {
        // Récupérer la vue depuis le MailMessage généré
        $mail = $notification->toMail($this);
        
        // Le MailMessage a une propriété view
        if (isset($mail->view) && $mail->view) {
            return $mail->view;
        }
    }
    
    // Mapping des notifications vers leurs vues
    $views = [
        'App\Notifications\NouvelleUtilisateurNotification' => 'emails.nouvelle-utilisateur',
        'App\Notifications\AgenceValideeNotification' => 'emails.agence-validee',
        'App\Notifications\AgenceRefuseeNotification' => 'emails.agence-refusee',
        'App\Notifications\AbonnementExpireNotification' => 'emails.abonnement-expire',
        'App\Notifications\NouvelleDemandeNotification' => 'emails.nouvelle-demande',
        'App\Notifications\NouvelleDemandeCompatibleNotification' => 'emails.nouvelle-demande-compatible',
        'App\Notifications\NouvellePropositionNotification' => 'emails.nouvelle-proposition',
        'App\Notifications\PropositionAccepteeNotification' => 'emails.proposition-acceptee',
        'App\Notifications\PropositionRefuseeNotification' => 'emails.proposition-refusee',
        'App\Notifications\RendezVousConfirmeNotification' => 'emails.rendez-vous-confirme',
        'App\Notifications\RendezVousAnnuleNotification' => 'emails.rendez-vous-annule',
        'App\Notifications\RendezVousTermineNotification' => 'emails.rendez-vous-termine',
        'App\Notifications\RappelVisiteNotification' => 'emails.rappel-visite',
        'App\Notifications\DemandeAnnuleeNotification' => 'emails.demande-annulee',
    ];

    return $views[get_class($notification)] ?? 'emails.nouvelle-utilisateur';
}

/**
 * Extrait les données d'une notification en accédant aux propriétés protégées
 */
protected function extractNotificationData($notification): array
{
    $data = [];
    $reflection = new \ReflectionObject($notification);
    
    foreach ($reflection->getProperties() as $property) {
        $property->setAccessible(true);
        $data[$property->getName()] = $property->getValue($notification);
    }
    
    return $data;
}

/**
 * Extrait les données d'une notification en accédant aux propriétés protégées
 */


    public static function notifyMany($users, $notification)
    {
        foreach ($users as $user) {
            $user->notify($notification);
        }
    }

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

    public function getUnreadNotificationsCount(): int
    {
        return $this->unreadNotifications()->count();
    }
    
    public function creneaux(): HasMany
    {
        return $this->hasMany(CreneauRendezVous::class);
    }

    // ✅ Envoyer automatiquement la notification à la création d'un utilisateur
    protected static function booted()
    {
        static::created(function ($user) {
            // Envoyer la notification automatiquement (email + base de données)
            try {
                $user->notify(new \App\Notifications\NouvelleUtilisateurNotification($user, $user->role));
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'envoi de la notification: ' . $e->getMessage());
            }
        });
    }
}
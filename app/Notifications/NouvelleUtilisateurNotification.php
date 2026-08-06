<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleUtilisateurNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $plainPassword;
    protected $role;

    public function __construct(User $user, string $plainPassword, string $role)
    {
        $this->user = $user;
        $this->plainPassword = $plainPassword;
        $this->role = $role;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $roleLabels = [
            'admin' => 'Administrateur',
            'agence' => 'Agence immobilière',
            'particulier' => 'Particulier',
        ];

        $roleLabel = $roleLabels[$this->role] ?? $this->role;

        return (new MailMessage)
            ->subject('Bienvenue sur DoyaImmo - Votre compte a été créé')
            ->greeting('Bonjour ' . $this->user->prenom . ' ' . $this->user->nom . ' !')
            ->line('Votre compte **' . $roleLabel . '** a été créé avec succès sur la plateforme DoyaImmo.')
            ->line('')
            ->line('**Voici vos informations de connexion :**')
            ->line('- **Email :** ' . $this->user->email)
            ->line('- **Mot de passe :** ' . $this->plainPassword)
            ->line('- **Rôle :** ' . $roleLabel)
            ->line('')
            ->line('🔐 Pour des raisons de sécurité, nous vous recommandons de changer votre mot de passe lors de votre première connexion.')
            ->action('Se connecter à DoyaImmo', url('/login'))
            ->line('')
            ->line('Nous vous souhaitons une excellente expérience sur DoyaImmo !')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Bienvenue sur DoyaImmo',
            'message' => 'Votre compte a été créé avec succès.',
            'type' => 'success',
            'icon' => 'fa-user-plus',
            'link' => route('login'),
            'role' => $this->role,
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
            'user_name' => $this->user->prenom . ' ' . $this->user->nom,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
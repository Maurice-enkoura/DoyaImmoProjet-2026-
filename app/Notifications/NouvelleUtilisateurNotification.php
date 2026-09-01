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
    protected $role;

    public function __construct(User $user, string $role)
    {
        $this->user = $user;
        $this->role = $role;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Bienvenue sur DoyaImmo - ' . $this->user->prenom . ' !')
            ->markdown('emails.nouvelle-utilisateur', [
                'user' => $this->user,
                'role' => $this->role,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $messages = [
            'particulier' => 'Bienvenue sur DoyaImmo ! Commencez à publier vos besoins immobiliers.',
            'agence' => 'Votre agence est en cours de validation par nos équipes.',
            'admin' => 'Bienvenue sur le tableau de bord administrateur.',
        ];

        $titles = [
            'particulier' => 'Bienvenue ! ',
            'agence' => 'Votre agence est en cours de validation ',
            'admin' => 'Bienvenue administrateur ! ',
        ];

        return [
            'title' => $titles[$this->role] ?? 'Bienvenue sur DoyaImmo',
            'message' => $messages[$this->role] ?? 'Votre compte a été créé avec succès.',
            'type' => 'success',
            'icon' => 'fa-user-plus',
            'link' => route('dashboard'),
            'user_id' => $this->user->id,
            'role' => $this->role,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
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
            ->view('emails.nouvelle-utilisateur', [   // ✅ view au lieu de markdown
                'user'       => $this->user,
                'role'       => $this->role,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $data = [
            'particulier' => [
                'title'   => 'Bienvenue ! 👋',
                'message' => 'Bienvenue sur DoyaImmo ! Commencez à publier vos besoins immobiliers.',
                'link'    => route('particulier.demandes.create'),
            ],
            'agence' => [
                'title'   => 'Votre agence est en cours de validation',
                'message' => 'Votre agence est en cours de validation par nos équipes.',
                'link'    => route('agence.profil'),
            ],
            'admin' => [
                'title'   => 'Bienvenue administrateur !',
                'message' => 'Bienvenue sur le tableau de bord administrateur.',
                'link'    => route('admin.dashboard'),
            ],
        ];

        $roleData = $data[$this->role] ?? [
            'title'   => 'Bienvenue sur DoyaImmo',
            'message' => 'Votre compte a été créé avec succès.',
            'link'    => null,
        ];

        return [
            'title'   => $roleData['title'],
            'message' => $roleData['message'],
            'type'    => 'success',
            'icon'    => 'fa-user-plus',
            'link'    => $roleData['link'],
            'user_id' => $this->user->id,
            'role'    => $this->role,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
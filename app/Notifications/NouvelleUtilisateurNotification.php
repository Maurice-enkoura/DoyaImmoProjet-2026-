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
        $roleLabels = [
            'admin' => 'Administrateur',
            'agence' => 'Agence immobilière',
            'particulier' => 'Particulier',
        ];

        $roleLabel = $roleLabels[$this->role] ?? $this->role;

        $mail = (new MailMessage)
            ->subject('Bienvenue sur DoyaImmo - ' . $this->user->prenom . ' !')
            ->greeting('Bonjour ' . $this->user->prenom . ' ' . $this->user->nom . ' !')
            ->line('Nous sommes ravis de vous accueillir sur **DoyaImmo** !')
            ->line('')
            ->line('Votre compte **' . $roleLabel . '** a été créé avec succès.')
            ->line('');

        if ($this->role === 'agence') {
            $mail->line('📌 Votre agence est en cours de validation par nos équipes.');
            $mail->line('Vous serez notifié dès que votre compte sera activé.');
        } elseif ($this->role === 'particulier') {
            $mail->line('🏠 Vous pouvez dès maintenant publier vos besoins et trouver le logement idéal.');
            $mail->line('Créez votre première demande de logement en quelques minutes.');
        }

        return $mail
            ->line('')
            ->action('Accéder à mon tableau de bord', route('dashboard'))
            ->line('')
            ->line('🔐 Pour toute question, n\'hésitez pas à contacter notre support.')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $messages = [
            'particulier' => 'Bienvenue sur DoyaImmo ! Commencez à publier vos besoins immobiliers.',
            'agence' => 'Votre agence est en cours de validation par nos équipes.',
            'admin' => 'Bienvenue sur le tableau de bord administrateur.',
        ];

        $titles = [
            'particulier' => 'Bienvenue ! 🏠',
            'agence' => 'Votre agence est en cours de validation 📌',
            'admin' => 'Bienvenue administrateur ! 👋',
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
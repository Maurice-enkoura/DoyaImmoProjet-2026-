<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class CustomResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        // Construire le lien de réinitialisation
        $url = URL::route('password.reset', ['token' => $this->token, 'email' => $notifiable->email]);

        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ' !')
            ->line('Vous recevez cet email car nous avons reçu une demande de réinitialisation de mot de passe pour votre compte DoyaImmo.')
            ->action('Cliquez ici pour réinitialiser votre mot de passe', $url)
            ->line('Si vous n\'avez pas demandé de réinitialisation, aucune action n\'est requise.')
            ->salutation('Cordialement, L\'équipe DoyaImmo');
    }
}
<?php

namespace App\Notifications;

use App\Models\Agence;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AgenceValideeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Agence $agence) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Agence validée !')
            ->greeting('Félicitations ' . $notifiable->nom . ' !')
            ->line('Votre agence "' . $this->agence->nom_agence . '" a été validée par l\'administrateur.')
            ->line('Vous pouvez maintenant publier vos biens immobiliers et faire des propositions.')
            ->action('Commencer', route('agence.dashboard'))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'agence_id' => $this->agence->id,
            'nom_agence' => $this->agence->nom_agence,
            'message' => 'Votre agence a été validée',
        ];
    }
}
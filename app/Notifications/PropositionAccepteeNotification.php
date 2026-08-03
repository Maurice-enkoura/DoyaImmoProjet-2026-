<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PropositionAccepteeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Proposition $proposition) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Proposition acceptée !')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Félicitations ! Votre proposition a été acceptée par le particulier.')
            ->line('Demande: ' . $this->proposition->demande->description)
            ->line('Prix proposé: ' . number_format($this->proposition->prix_propose, 2) . ' €')
            ->action('Planifier un rendez-vous', route('agence.rendezvous.create', $this->proposition))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'proposition_id' => $this->proposition->id,
            'demande_id' => $this->proposition->demande_id,
            'message' => 'Votre proposition a été acceptée',
        ];
    }
}
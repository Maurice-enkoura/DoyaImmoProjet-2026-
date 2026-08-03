<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NouvellePropositionNotification extends Notification implements ShouldQueue
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
            ->subject('Nouvelle proposition pour votre demande')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Une nouvelle proposition a été faite pour votre demande immobilière.')
            ->line('Agence: ' . $this->proposition->agence->nom_agence)
            ->line('Prix proposé: ' . number_format($this->proposition->prix_propose, 2) . ' €')
            ->line('Message: ' . $this->proposition->message)
            ->action('Voir la proposition', route('particulier.propositions.show', $this->proposition))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'proposition_id' => $this->proposition->id,
            'agence_id' => $this->proposition->agence_id,
            'nom_agence' => $this->proposition->agence->nom_agence,
            'prix_propose' => $this->proposition->prix_propose,
            'message' => 'Nouvelle proposition reçue',
        ];
    }
}
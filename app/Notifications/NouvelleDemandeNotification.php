<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NouvelleDemandeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected DemandeImmobiliere $demande) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nouvelle demande immobilière')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Une nouvelle demande a été publiée par un particulier.')
            ->line('Type d\'opération: ' . $this->demande->type_operation->label())
            ->line('Type de bien: ' . $this->demande->type_bien->label())
            ->line('Budget maximum: ' . number_format($this->demande->budget_maximum, 2) . ' €')
            ->line('Zone recherchée: ' . $this->demande->zone_recherchee)
            ->action('Voir la demande', route('agence.demandes.show', $this->demande))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'demande_id' => $this->demande->id,
            'type_operation' => $this->demande->type_operation->value,
            'type_bien' => $this->demande->type_bien->value,
            'budget_maximum' => $this->demande->budget_maximum,
            'zone_recherchee' => $this->demande->zone_recherchee,
            'message' => 'Nouvelle demande immobilière publiée',
        ];
    }
}
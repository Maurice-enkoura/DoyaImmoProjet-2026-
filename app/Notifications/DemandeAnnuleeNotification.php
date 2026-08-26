<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DemandeAnnuleeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $demande;

    public function __construct(DemandeImmobiliere $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $demande = $this->demande;

        return (new MailMessage)
            ->subject('🚫 Demande annulée - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Le particulier a annulé sa demande de logement :')
            ->line('')
            ->line('**Type de bien :** ' . ($demande->type_bien->label() ?? $demande->type_bien))
            ->line('**Zone recherchée :** ' . $demande->zone_recherchee)
            ->line('**Budget :** ' . number_format($demande->budget_maximum, 0, ',', ' ') . ' FCFA')
            ->line('')
            ->line('Les offres que vous avez envoyées pour cette demande ne sont plus valables.')
            ->line('N\'hésitez pas à consulter d\'autres demandes disponibles.')
            ->action('Voir les demandes', route('agence.demandes.index'))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $demande = $this->demande;

        return [
            'title' => '🚫 Demande annulée',
            'message' => 'La demande de ' . ($demande->type_bien->label() ?? 'bien') . ' à ' . $demande->zone_recherchee . ' a été annulée par le particulier.',
            'type' => 'warning',
            'icon' => 'fa-circle-exclamation',
            'link' => route('agence.demandes.index'),
            'demande_id' => $demande->id,
            'zone' => $demande->zone_recherchee,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
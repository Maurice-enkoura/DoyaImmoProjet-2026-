<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeNotification extends Notification implements ShouldQueue
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
            ->subject('🏠 Nouvelle demande de logement - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Un nouveau besoin de logement vient d\'être publié par un particulier :')
            ->line('')
            ->line('**Type de bien :** ' . ($demande->type_bien->label() ?? $demande->type_bien))
            ->line('**Type d\'opération :** ' . ($demande->type_operation->label() ?? $demande->type_operation))
            ->line('**Budget :** ' . number_format($demande->budget_maximum, 0, ',', ' ') . ' FCFA')
            ->line('**Zone recherchée :** ' . $demande->zone_recherchee)
            ->line('')
            ->line('Cette demande correspond peut-être à l\'un de vos biens.')
            ->action('Voir la demande', route('agence.demandes.show', $demande))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $demande = $this->demande;

        return [
            'title' => '🏠 Nouvelle demande de logement',
            'message' => 'Un nouveau besoin de ' . ($demande->type_bien->label() ?? $demande->type_bien) . ' à ' . $demande->zone_recherchee . ' vient d\'être publié.',
            'type' => 'info',
            'icon' => 'fa-house-circle-check',
            'link' => route('agence.demandes.show', $demande),
            'demande_id' => $demande->id,
            'zone' => $demande->zone_recherchee,
            'budget' => $demande->budget_maximum,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropositionAcceptationAnnuleeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $proposition;

    public function __construct(Proposition $proposition)
    {
        $this->proposition = $proposition;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $proposition = $this->proposition;

        return (new MailMessage)
            ->subject(' Acceptation annulée - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Le particulier a **annulé l\'acceptation** de votre proposition.')
            ->line('')
            ->line('**Bien proposé :** ' . ($proposition->bien->titre ?? 'N/A'))
            ->line('**Prix proposé :** ' . number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA')
            ->line('**Demande concernée :** ' . ($proposition->demande->type_bien->label() ?? 'bien') . ' à ' . ($proposition->demande->zone_recherchee ?? 'N/A'))
            ->line('')
            ->line('La demande du particulier est de nouveau **en attente**. Votre proposition reste visible et peut à nouveau être sélectionnée.')
            ->action('Voir la proposition', route('agence.propositions.show', $proposition))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $proposition = $this->proposition;

        return [
            'title'          => 'Acceptation annulée',
            'message'        => 'Le particulier a annulé l\'acceptation de votre proposition pour ' . ($proposition->bien->titre ?? 'un bien') . '. La demande est de nouveau en attente.',
            'type'           => 'warning',
            'icon'           => 'fa-rotate-left',
            'link'           => route('agence.propositions.show', $proposition),
            'proposition_id' => $proposition->id,
            'demande_id'     => $proposition->demande_id,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
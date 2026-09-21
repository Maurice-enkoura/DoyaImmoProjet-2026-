<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropositionReactiveeNotification extends Notification implements ShouldQueue
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
            ->subject(' Proposition réactivée - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Bonne nouvelle ! La demande d\'un particulier est de nouveau **en attente**, et votre proposition est **réactivée**.')
            ->line('')
            ->line('**Bien proposé :** ' . ($proposition->bien->titre ?? 'N/A'))
            ->line('**Prix proposé :** ' . number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA')
            ->line('**Demande concernée :** ' . ($proposition->demande->type_bien->label() ?? 'bien') . ' à ' . ($proposition->demande->zone_recherchee ?? 'N/A'))
            ->line('')
            ->line('Votre proposition est de nouveau visible par le particulier.')
            ->action('Voir la proposition', route('agence.propositions.show', $proposition))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $proposition = $this->proposition;

        return [
            'title'          => 'Proposition réactivée',
            'message'        => 'La demande du particulier est de nouveau en attente. Votre proposition pour ' . ($proposition->bien->titre ?? 'un bien') . ' est réactivée.',
            'type'           => 'info',
            'icon'           => 'fa-rotate-right',
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
<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropositionAnnuleeParAgenceNotification extends Notification implements ShouldQueue
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
        $etaitAcceptee = $proposition->statut === \App\Enums\StatutPropositionEnum::REFUSEE;

        return (new MailMessage)
            ->subject('❌ Proposition retirée - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('L\'agence a retiré sa proposition pour votre demande.')
            ->line('')
            ->line('**Agence :** ' . ($proposition->agence->nom_agence ?? 'N/A'))
            ->line('**Bien concerné :** ' . ($proposition->bien->titre ?? 'N/A'))
            ->line('**Prix proposé :** ' . number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA')
            ->line('')
            ->line('Votre demande repasse en attente. Vous pouvez consulter d\'autres propositions.')
            ->action('Voir mes offres', route('particulier.propositions.index'))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $proposition = $this->proposition;

        return [
            'title'          => 'Proposition retirée',
            'message'        => 'L\'agence ' . ($proposition->agence->nom_agence ?? '') . ' a retiré sa proposition pour votre demande.',
            'type'           => 'warning',
            'icon'           => 'fa-circle-xmark',
            'link'           => route('particulier.propositions.show', $proposition),
            'proposition_id' => $proposition->id,
            'demande_id'     => $proposition->demande_id,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
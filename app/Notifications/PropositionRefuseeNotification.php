<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropositionRefuseeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $proposition;
    protected $motif;

    public function __construct(Proposition $proposition, $motif = null)
    {
        $this->proposition = $proposition;
        $this->motif = $motif;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $proposition = $this->proposition;

        $message = (new MailMessage)
            ->subject('❌ Proposition refusée - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Votre proposition pour la demande de ' . ($proposition->demande->type_bien->label() ?? 'bien') . ' a été refusée.')
            ->line('')
            ->line('**Bien proposé :** ' . ($proposition->bien->titre ?? 'N/A'))
            ->line('**Prix proposé :** ' . number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA');

        if ($this->motif) {
            $message->line('')
                ->line('**Motif du refus :**')
                ->line($this->motif);
        }

        $message->line('')
            ->line('Nous vous encourageons à consulter d\'autres demandes qui pourraient correspondre à vos biens.')
            ->action('Voir les demandes', route('agence.demandes.index'))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        $proposition = $this->proposition;

        return [
            'title' => ' Proposition refusée',
            'message' => 'Votre proposition pour ' . ($proposition->bien->titre ?? 'un bien') . ' a été refusée.',
            'type' => 'danger',
            'icon' => 'fa-circle-xmark',
            'link' => route('agence.propositions.show', $proposition),
            'proposition_id' => $proposition->id,
            'demande_id' => $proposition->demande_id,
            'motif' => $this->motif,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
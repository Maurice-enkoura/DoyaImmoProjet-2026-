<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvellePropositionNotification extends Notification implements ShouldQueue
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
        $agence = $this->proposition->agence;

        return (new MailMessage)
            ->subject('💼 Nouvelle proposition pour votre demande')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('L\'agence **' . $agence->nom_agence . '** a fait une proposition pour votre demande :')
            ->line('**Prix proposé :** ' . number_format($this->proposition->prix_propose, 0, ',', ' ') . ' FCFA')
            ->line('**Bien :** ' . ($this->proposition->bien->titre ?? 'N/A'))
            ->action('Voir la proposition', url('/particulier/propositions/' . $this->proposition->id))
            ->line('Vous pouvez accepter ou refuser cette proposition.')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nouvelle proposition',
            'message' => 'Vous avez reçu une nouvelle proposition de ' . $this->proposition->agence->nom_agence,
            'type' => 'info',
            'icon' => 'fa-handshake',
            'link' => route('particulier.propositions.show', $this->proposition),
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
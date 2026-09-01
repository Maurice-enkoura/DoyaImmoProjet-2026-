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
        return (new MailMessage)
            ->subject(' Nouvelle proposition pour votre demande')
            ->markdown('emails.nouvelle-proposition', [
                'proposition' => $this->proposition,
                'notifiable' => $notifiable,
            ]);
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
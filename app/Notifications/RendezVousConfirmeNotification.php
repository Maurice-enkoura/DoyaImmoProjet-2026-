<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousConfirmeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected RendezVous $rendezVous)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Rendez-vous confirmé — DoyaImmo')
            ->view('emails.rendez-vous-confirme', [
                'rendezVous' => $this->rendezVous,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title'         => 'Rendez-vous confirmé',
            'message'       => 'Votre rendez-vous du ' . ($this->rendezVous->date_visite ? $this->rendezVous->date_visite->format('d/m/Y') : 'N/A') . ' a été confirmé.',
            'type'          => 'success',
            'icon'          => 'fa-calendar-check',
            'link'          => route('particulier.rendezvous.show', $this->rendezVous),
            'rendezvous_id' => $this->rendezVous->id,
            'agence_nom'    => $this->rendezVous->agence->nom_agence ?? 'N/A',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
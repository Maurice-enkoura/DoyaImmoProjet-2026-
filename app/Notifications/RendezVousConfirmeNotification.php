<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RendezVousConfirmeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected RendezVous $rendezVous) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Rendez-vous confirmé')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Un rendez-vous a été confirmé.')
            ->line('Date: ' . $this->rendezVous->date_visite->format('d/m/Y'))
            ->line('Heure: ' . $this->rendezVous->heure_visite->format('H:i'))
            ->line('Bien: ' . $this->rendezVous->proposition->bien->titre)
            ->action('Voir le rendez-vous', route('agence.rendezvous.show', $this->rendezVous))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'rendezvous_id' => $this->rendezVous->id,
            'date_visite' => $this->rendezVous->date_visite->format('d/m/Y'),
            'heure_visite' => $this->rendezVous->heure_visite->format('H:i'),
            'message' => 'Rendez-vous confirmé',
        ];
    }
}
<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RendezVousAnnuleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected RendezVous $rendezVous,
        protected ?string $motif = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Rendez-vous annulé')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Un rendez-vous a été annulé par l\'agence.')
            ->line('Date: ' . $this->rendezVous->date_visite->format('d/m/Y'))
            ->line('Heure: ' . $this->rendezVous->heure_visite->format('H:i'));

        if ($this->motif) {
            $mail->line('Motif: ' . $this->motif);
        }

        return $mail->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'rendezvous_id' => $this->rendezVous->id,
            'date_visite' => $this->rendezVous->date_visite->format('d/m/Y'),
            'heure_visite' => $this->rendezVous->heure_visite->format('H:i'),
            'motif' => $this->motif,
            'message' => 'Rendez-vous annulé',
        ];
    }
}
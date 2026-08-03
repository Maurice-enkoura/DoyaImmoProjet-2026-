<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class RendezVousTermineNotification extends Notification implements ShouldQueue
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
            ->subject('Visite terminée')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('La visite a été marquée comme terminée.')
            ->line('Date: ' . $this->rendezVous->date_visite->format('d/m/Y'))
            ->line('Heure: ' . $this->rendezVous->heure_visite->format('H:i'))
            ->line('Nous vous invitons à évaluer votre expérience avec cette agence.')
            ->action('Évaluer l\'agence', route('particulier.evaluations.create', $this->rendezVous->agence))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'rendezvous_id' => $this->rendezVous->id,
            'agence_id' => $this->rendezVous->agence_id,
            'date_visite' => $this->rendezVous->date_visite->format('d/m/Y'),
            'message' => 'Visite terminée - Veuillez évaluer l\'agence',
        ];
    }
}
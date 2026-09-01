<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousAnnuleNotification extends Notification implements ShouldQueue
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
        $agence = $this->rendezVous->agence;
        $dateVisite = $this->rendezVous->date_visite ? $this->rendezVous->date_visite->format('d/m/Y') : 'N/A';
        $heureVisite = $this->rendezVous->heure_visite ?? $this->rendezVous->creneau->heure_debut ?? 'N/A';

        $message = (new MailMessage)
            ->subject('Rendez-vous annulé - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Votre rendez-vous a été annulé :')
            ->line('')
            ->line('**Date :** ' . $dateVisite)
            ->line('**Heure :** ' . $heureVisite);

        if ($agence) {
            $message->line('**Agence :** ' . $agence->nom_agence);
        }

        $message->line('')
                ->line('Vous pouvez planifier un nouveau rendez-vous à tout moment.')
                ->action('Planifier un rendez-vous', url('/particulier/rendezvous/create'))
                ->line('')
                ->line('Nous sommes désolés pour ce contretemps.')
                ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Rendez-vous annulé',
            'message' => 'Votre rendez-vous du ' . ($this->rendezVous->date_visite ? $this->rendezVous->date_visite->format('d/m/Y') : 'N/A') . ' a été annulé.',
            'type' => 'error',
            'icon' => 'fa-calendar-xmark',
            'link' => url('/particulier/rendezvous/create'),
            'rendezvous_id' => $this->rendezVous->id,
            'agence_nom' => $this->rendezVous->agence->nom_agence ?? 'N/A',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
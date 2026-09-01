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
        $agence = $this->rendezVous->agence;
        $dateVisite = $this->rendezVous->date_visite ? $this->rendezVous->date_visite->format('d/m/Y') : 'N/A';
        $heureVisite = $this->rendezVous->heure_visite ?? $this->rendezVous->creneau->heure_debut ?? 'N/A';

        $message = (new MailMessage)
            ->subject('Rendez-vous confirmé - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Votre rendez-vous a été confirmé :')
            ->line('')
            ->line('**Date :** ' . $dateVisite)
            ->line('**Heure :** ' . $heureVisite);

        if ($agence) {
            $message->line('**Agence :** ' . $agence->nom_agence);
            if ($agence->adresse) {
                $message->line('**Adresse :** ' . $agence->adresse);
            }
            if ($agence->user && $agence->user->telephone) {
                $message->line('**Téléphone :** ' . $agence->user->telephone);
            }
        }

        $message->line('')
                ->line('Merci de vous présenter à l\'heure convenue.')
                ->action('Voir le rendez-vous', url('/particulier/rendezvous/' . $this->rendezVous->id))
                ->line('')
                ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Rendez-vous confirmé',
            'message' => 'Votre rendez-vous du ' . ($this->rendezVous->date_visite ? $this->rendezVous->date_visite->format('d/m/Y') : 'N/A') . ' a été confirmé.',
            'type' => 'success',
            'icon' => 'fa-calendar-check',
            'link' => route('particulier.rendezvous.show', $this->rendezVous),
            'rendezvous_id' => $this->rendezVous->id,
            'agence_nom' => $this->rendezVous->agence->nom_agence ?? 'N/A',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousTermineNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $rendezVous;

    public function __construct(RendezVous $rendezVous)
    {
        $this->rendezVous = $rendezVous;
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
            ->subject('✅ Rendez-vous terminé - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Votre rendez-vous a été marqué comme terminé :')
            ->line('')
            ->line('**Date :** ' . $dateVisite)
            ->line('**Heure :** ' . $heureVisite);

        if ($agence) {
            $message->line('**Agence :** ' . $agence->nom_agence);
        }

        $message->line('')
            ->line('Merci d\'avoir utilisé DoyaImmo pour votre recherche immobilière.')
            ->line('N\'oubliez pas de laisser un avis sur cette agence pour aider les autres utilisateurs !')
            ->action('Laisser un avis', route('particulier.evaluations.create', $agence->id))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Rendez-vous terminé',
            'message' => 'Votre rendez-vous du ' . ($this->rendezVous->date_visite ? $this->rendezVous->date_visite->format('d/m/Y') : 'N/A') . ' a été marqué comme terminé.',
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
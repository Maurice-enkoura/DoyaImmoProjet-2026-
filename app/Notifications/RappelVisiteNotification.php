<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RappelVisiteNotification extends Notification implements ShouldQueue
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
        $rdv = $this->rendezVous;
        $agence = $rdv->agence;

        $message = (new MailMessage)
            ->subject('🔔 Rappel de votre rendez-vous - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Ceci est un rappel pour votre rendez-vous prévu :')
            ->line('')
            ->line('**Date :** ' . $rdv->date_visite->format('d/m/Y'))
            ->line('**Heure :** ' . ($rdv->heure_visite ?? 'N/A'))
            ->line('**Agence :** ' . ($agence->nom_agence ?? 'N/A'));

        if ($agence && $agence->adresse) {
            $message->line('**Adresse :** ' . $agence->adresse);
        }

        if ($agence && $agence->user && $agence->user->telephone) {
            $message->line('**Téléphone de l\'agence :** ' . $agence->user->telephone);
        }

        $message->line('')
            ->line('Merci de vous présenter à l\'heure convenue.')
            ->line('En cas d\'imprévu, n\'hésitez pas à contacter l\'agence.')
            ->action('Voir le rendez-vous', route('particulier.rendezvous.show', $rdv))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        $rdv = $this->rendezVous;

        return [
            'title' => ' Rappel de rendez-vous',
            'message' => 'Rappel : Vous avez un rendez-vous le ' . $rdv->date_visite->format('d/m/Y') . ' à ' . ($rdv->heure_visite ?? 'N/A') . ' avec ' . ($rdv->agence->nom_agence ?? 'l\'agence'),
            'type' => 'info',
            'icon' => 'fa-bell',
            'link' => route('particulier.rendezvous.show', $rdv),
            'rendezvous_id' => $rdv->id,
            'date_visite' => $rdv->date_visite->format('d/m/Y'),
            'heure_visite' => $rdv->heure_visite,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
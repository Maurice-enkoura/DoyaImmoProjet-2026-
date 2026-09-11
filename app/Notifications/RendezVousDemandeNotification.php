<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousDemandeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected RendezVous $rendezVous
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $client = $this->rendezVous->particulier->user->prenom ?? 'Client';
        $bien = $this->rendezVous->proposition->bien->titre ?? 'Bien';
        $date = $this->rendezVous->date_visite->format('d/m/Y');
        $heure = \Carbon\Carbon::parse($this->rendezVous->heure_visite)->format('H:i');

        return (new MailMessage)
            ->subject(' Nouvelle demande de visite — DoyaImmo')
            ->markdown('emails.rendez-vous-demande', [
                'rendezVous' => $this->rendezVous,
                'client' => $client,
                'bien' => $bien,
                'date' => $date,
                'heure' => $heure,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => ' Nouvelle demande de visite',
            'message' => $this->rendezVous->particulier->user->prenom . ' ' . $this->rendezVous->particulier->user->nom . ' a demandé une visite pour le ' . $this->rendezVous->date_visite->format('d/m/Y') . ' à ' . \Carbon\Carbon::parse($this->rendezVous->heure_visite)->format('H:i'),
            'type' => 'info',
            'icon' => 'fa-calendar-plus',
            'link' => route('agence.rendezvous.show', $this->rendezVous),
            'rendez_vous_id' => $this->rendezVous->id,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
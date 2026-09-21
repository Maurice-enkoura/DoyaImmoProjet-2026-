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
        return (new MailMessage)
            ->subject(' Nouvelle demande de visite — DoyaImmo')
            ->view('emails.rendez-vous-demande', [
                'rendezVous' => $this->rendezVous,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $rdv   = $this->rendezVous;
        $bien  = $rdv->proposition->bien ?? null;
        $client = $rdv->particulier->user ?? null;

        return [
            'title'          => 'Nouvelle demande de visite',
            'message'        => 'Un client souhaite visiter "' . ($bien->titre ?? 'un bien') . '".',
            'type'           => 'info',
            'icon'           => 'fa-calendar-plus',
            'link'           => route('agence.rendezvous.show', $rdv),
            'rendez_vous_id' => $rdv->id,
            'date_visite'    => $rdv->date_visite?->format('d/m/Y'),
            'heure_visite'   => $rdv->heure_visite,
            'client_nom'     => $client ? ($client->prenom . ' ' . $client->nom) : 'Client',
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
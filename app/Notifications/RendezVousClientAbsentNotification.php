<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousClientAbsentNotification extends Notification implements ShouldQueue
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
            ->subject(' Visite non honorée — DoyaImmo')
            ->view('emails.rendez-vous-client-absent', [
                'rendezVous' => $this->rendezVous,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $rdv  = $this->rendezVous;
        $bien = $rdv->proposition->bien ?? null;

        return [
            'title'          => 'Visite non honorée',
            'message'        => 'L\'agence a marqué votre visite de "' . ($bien->titre ?? 'bien') . '" comme non honorée.',
            'type'           => 'warning',
            'icon'           => 'fa-user-slash',
            'link'           => route('particulier.rendezvous.show', $rdv),
            'rendez_vous_id' => $rdv->id,
            'proposition_id' => $rdv->proposition_id,
            'agence_id'      => $rdv->agence_id,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
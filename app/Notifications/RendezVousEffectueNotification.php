<?php

namespace App\Notifications;

use App\Models\RendezVous;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RendezVousEffectueNotification extends Notification implements ShouldQueue
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
            ->subject(' Votre visite a été validée — DoyaImmo')
            ->view('emails.rendez-vous-effectue', [
                'rendezVous' => $this->rendezVous,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $rdv  = $this->rendezVous;
        $bien = $rdv->proposition->bien ?? null;

        return [
            'title'          => 'Visite validée',
            'message'        => 'Votre visite de "' . ($bien->titre ?? 'bien') . '" a été validée. Vous pouvez évaluer l\'agence.',
            'type'           => 'success',
            'icon'           => 'fa-circle-check',
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
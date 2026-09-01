<?php

namespace App\Notifications;
use App\Models\Agence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgenceRefuseeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Agence $agence, protected string $motif)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Demande de validation agence - DoyaImmo')
            ->markdown('emails.agence-refusee', [
                'agence' => $this->agence,
                'motif' => $this->motif,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Agence refusée',
            'message' => 'Votre agence a été refusée. Motif : ' . $this->motif,
            'type' => 'error',
            'icon' => 'fa-times-circle',
            'link' => route('agence.profil'),
            'agence_id' => $this->agence->id,
            'motif' => $this->motif,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
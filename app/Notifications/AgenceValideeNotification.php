<?php

namespace App\Notifications;

use App\Models\Agence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgenceValideeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Agence $agence)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Votre agence DoyaImmo a été validée')
            ->markdown('emails.agence-validee', [
                'agence' => $this->agence,
                'user' => $notifiable, // ✅ Passer l'utilisateur
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Agence validée',
            'message' => 'Votre agence ' . $this->agence->nom_agence . ' a été validée avec succès.',
            'type' => 'success',
            'icon' => 'fa-check-circle',
            'link' => route('agence.dashboard'),
            'agence_id' => $this->agence->id,
            'agence_nom' => $this->agence->nom_agence,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
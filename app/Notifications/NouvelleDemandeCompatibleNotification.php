<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeCompatibleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected DemandeImmobiliere $demande, protected int $score)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Nouvelle demande compatible avec vos biens')
            ->markdown('emails.nouvelle-demande-compatible', [
                'demande' => $this->demande,
                'score' => $this->score,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nouvelle demande compatible',
            'message' => 'Un nouveau besoin compatible avec vos biens est disponible.',
            'type' => 'info',
            'icon' => 'fa-bell',
            'link' => route('agence.demandes.show', $this->demande),
            'demande_id' => $this->demande->id,
            'score' => $this->score,
            'zone_recherchee' => $this->demande->zone_recherchee,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
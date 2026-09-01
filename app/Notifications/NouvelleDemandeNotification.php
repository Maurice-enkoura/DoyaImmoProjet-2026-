<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $demande;

    public function __construct(DemandeImmobiliere $demande)
    {
        $this->demande = $demande;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Nouvelle demande de logement - DoyaImmo')
            ->markdown('emails.nouvelle-demande', [
                'demande' => $this->demande,
                'user' => $notifiable, // ✅ Passer l'utilisateur
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $demande = $this->demande;

        return [
            'title' => ' Nouvelle demande de logement',
            'message' => 'Un nouveau besoin de ' . ($demande->type_bien->label() ?? $demande->type_bien) . ' à ' . $demande->zone_recherchee . ' vient d\'être publié.',
            'type' => 'info',
            'icon' => 'fa-house-circle-check',
            'link' => route('agence.demandes.show', $demande),
            'demande_id' => $demande->id,
            'zone' => $demande->zone_recherchee,
            'budget' => $demande->budget_maximum,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
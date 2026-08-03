<?php

namespace App\Notifications;

use App\Models\Abonnement;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AbonnementExpirantNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Abonnement $abonnement) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Votre abonnement va expirer')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Votre abonnement ' . $this->abonnement->formule->label() . ' va expirer le ' . $this->abonnement->date_fin->format('d/m/Y'))
            ->line('Pour continuer à publier des biens et faire des propositions, veuillez renouveler votre abonnement.')
            ->action('Renouveler mon abonnement', route('agence.abonnement'))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        return [
            'abonnement_id' => $this->abonnement->id,
            'formule' => $this->abonnement->formule->value,
            'date_fin' => $this->abonnement->date_fin->format('d/m/Y'),
            'message' => 'Votre abonnement va expirer',
        ];
    }
}
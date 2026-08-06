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
            ->subject('❌ Demande de validation agence - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ' !')
            ->line('Nous avons examiné votre demande de validation pour l\'agence **' . $this->agence->nom_agence . '**.')
            ->line('')
            ->line('**Motif du refus :**')
            ->line($this->motif)
            ->line('')
            ->line('Veuillez corriger les points mentionnés et soumettre à nouveau votre demande.')
            ->action('Soumettre à nouveau', url('/agence/profil'))
            ->line('')
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
            ->salutation('L\'équipe DoyaImmo');
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
<?php

namespace App\Notifications;

use App\Models\Agence;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class AgenceRefuseeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Agence $agence,
        protected ?string $motif = null
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Agence non validée')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Nous regrettons de vous informer que votre agence "' . $this->agence->nom_agence . '" n\'a pas été validée.');

        if ($this->motif) {
            $mail->line('Motif: ' . $this->motif);
        }

        $mail->line('Veuillez contacter l\'administrateur pour plus d\'informations.')
            ->line('Merci d\'utiliser DoyaImmo !');

        return $mail;
    }

    public function toArray($notifiable): array
    {
        return [
            'agence_id' => $this->agence->id,
            'nom_agence' => $this->agence->nom_agence,
            'motif' => $this->motif,
            'message' => 'Votre agence a été refusée',
        ];
    }
}
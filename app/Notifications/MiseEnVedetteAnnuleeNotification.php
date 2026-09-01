<?php

namespace App\Notifications;

use App\Models\MiseEnVedette;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MiseEnVedetteAnnuleeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $miseEnVedette;

    public function __construct(MiseEnVedette $miseEnVedette)
    {
        $this->miseEnVedette = $miseEnVedette;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $bien = $this->miseEnVedette->bien;
        $titreBien = $bien->titre ?? 'Votre bien';
        $motif = $this->miseEnVedette->commentaire_admin ?? 'Aucun motif spécifié';

        return (new MailMessage)
            ->subject(' Annulation de la mise en vedette - DoyaImmo')
            ->greeting('Bonjour,')
            ->line('Nous vous informons que votre demande de mise en vedette a été annulée.')
            ->line("**Bien concerné :** {$titreBien}")
            ->line("**Motif :** {$motif}")
            ->line('Si vous avez des questions, n\'hésitez pas à nous contacter.')
            ->action('Voir mon bien', route('agence.biens.show', $bien))
            ->salutation('Cordialement, L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $bien = $this->miseEnVedette->bien;
        $titreBien = $bien->titre ?? 'Votre bien';
        $motif = $this->miseEnVedette->commentaire_admin ?? 'Aucun motif spécifié';

        return [
            'title' => ' Mise en vedette annulée',
            'message' => "La mise en vedette de votre bien \"{$titreBien}\" a été annulée.",
            'type' => 'danger',
            'icon' => 'fa-circle-xmark',
            'link' => route('agence.biens.show', $bien),
            'mise_vedette_id' => $this->miseEnVedette->id,
            'bien_id' => $bien->id,
            'motif' => $motif,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
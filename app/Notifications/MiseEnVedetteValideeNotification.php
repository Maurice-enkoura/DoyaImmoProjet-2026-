<?php

namespace App\Notifications;

use App\Models\MiseEnVedette;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MiseEnVedetteValideeNotification extends Notification implements ShouldQueue
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
        $duree = $this->miseEnVedette->duree;
        $dateFin = $this->miseEnVedette->date_fin->format('d/m/Y');

        return (new MailMessage)
            ->subject(' Votre bien est en vedette ! - DoyaImmo')
            ->greeting('Bonjour,')
            ->line('Nous avons le plaisir de vous informer que votre demande de mise en vedette a été validée.')
            ->line("**Bien concerné :** {$titreBien}")
            ->line("**Durée :** {$duree} jours")
            ->line("**Date de fin :** {$dateFin}")
            ->line('Votre bien est désormais visible en vedette sur DoyaImmo.')
            ->action('Voir mon bien', route('agence.biens.show', $bien))
            ->line('Merci de faire confiance à DoyaImmo.')
            ->salutation('Cordialement, L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $bien = $this->miseEnVedette->bien;
        $titreBien = $bien->titre ?? 'Votre bien';
        $duree = $this->miseEnVedette->duree;
        $dateFin = $this->miseEnVedette->date_fin->format('d/m/Y');

        return [
            'title' => ' Mise en vedette activée',
            'message' => "Votre bien \"{$titreBien}\" est maintenant en vedette pour {$duree} jours (jusqu'au {$dateFin}).",
            'type' => 'success',
            'icon' => 'fa-star',
            'link' => route('agence.biens.show', $bien),
            'mise_vedette_id' => $this->miseEnVedette->id,
            'bien_id' => $bien->id,
            'duree' => $duree,
            'date_fin' => $dateFin,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
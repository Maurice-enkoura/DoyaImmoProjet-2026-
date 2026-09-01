<?php

namespace App\Notifications;

use App\Models\MiseEnVedette;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeVedetteNotification extends Notification implements ShouldQueue
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
        $agence = $this->miseEnVedette->agence;
        $bien = $this->miseEnVedette->bien;
        $nomAgence = $agence->nom_agence ?? 'Agence';
        $titreBien = $bien->titre ?? 'Bien';
        $duree = $this->miseEnVedette->duree;
        $montant = number_format($this->miseEnVedette->montant, 0, ',', ' ');

        return (new MailMessage)
            ->subject(' Nouvelle demande de mise en vedette - DoyaImmo')
            ->greeting('Bonjour Administrateur,')
            ->line('Une nouvelle demande de mise en vedette a été soumise.')
            ->line("**Agence :** {$nomAgence}")
            ->line("**Bien :** {$titreBien}")
            ->line("**Durée :** {$duree} jours")
            ->line("**Montant :** {$montant} FCFA")
            ->action('Voir la demande', route('admin.mises-vedette.show', $this->miseEnVedette))
            ->salutation('Cordialement, L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $agence = $this->miseEnVedette->agence;
        $bien = $this->miseEnVedette->bien;
        $nomAgence = $agence->nom_agence ?? 'Agence';
        $titreBien = $bien->titre ?? 'Bien';
        $duree = $this->miseEnVedette->duree;

        return [
            'title' => ' Nouvelle demande de mise en vedette',
            'message' => "L'agence \"{$nomAgence}\" demande une mise en vedette pour le bien \"{$titreBien}\" ({$duree} jours).",
            'type' => 'warning',
            'icon' => 'fa-star',
            'link' => route('admin.mises-vedette.show', $this->miseEnVedette),
            'mise_vedette_id' => $this->miseEnVedette->id,
            'agence_id' => $agence->id,
            'bien_id' => $bien->id,
            'duree' => $duree,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
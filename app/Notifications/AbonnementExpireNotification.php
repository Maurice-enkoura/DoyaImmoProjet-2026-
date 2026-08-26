<?php

namespace App\Notifications;

use App\Models\Abonnement;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AbonnementExpireNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $abonnement;
    protected $joursRestants;

    public function __construct(Abonnement $abonnement, $joursRestants)
    {
        $this->abonnement = $abonnement;
        $this->joursRestants = $joursRestants;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $abonnement = $this->abonnement;
        $jours = $this->joursRestants;

        $message = (new MailMessage)
            ->subject('⚠️ Votre abonnement expire bientôt - DoyaImmo')
            ->greeting('Bonjour ' . $notifiable->prenom . ' !')
            ->line('Votre abonnement ' . ($abonnement->formule->label() ?? $abonnement->formule) . ' expire dans ' . $jours . ' jour' . ($jours > 1 ? 's' : '') . '.');

        if ($jours <= 1) {
            $message->line('⚠️ **Dernier jour pour renouveler votre abonnement !**');
        } elseif ($jours <= 3) {
            $message->line('⚠️ **Plus que ' . $jours . ' jours avant l\'expiration !**');
        }

        $message->line('')
            ->line('**Date d\'expiration :** ' . $abonnement->date_fin->format('d/m/Y'))
            ->line('**Formule actuelle :** ' . ($abonnement->formule->label() ?? $abonnement->formule))
            ->line('')
            ->line('Pour continuer à bénéficier de tous les avantages, renouvelez votre abonnement dès maintenant.')
            ->action('Renouveler mon abonnement', route('agence.abonnement'))
            ->line('')
            ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        $abonnement = $this->abonnement;
        $jours = $this->joursRestants;

        $type = $jours <= 1 ? 'danger' : ($jours <= 3 ? 'warning' : 'info');

        return [
            'title' => '⚠️ Abonnement expire dans ' . $jours . ' jour' . ($jours > 1 ? 's' : ''),
            'message' => 'Votre abonnement ' . ($abonnement->formule->label() ?? $abonnement->formule) . ' expire le ' . $abonnement->date_fin->format('d/m/Y') . '. Renouvelez-le dès maintenant.',
            'type' => $type,
            'icon' => 'fa-clock',
            'link' => route('agence.abonnement'),
            'abonnement_id' => $abonnement->id,
            'date_fin' => $abonnement->date_fin->format('d/m/Y'),
            'jours_restants' => $jours,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
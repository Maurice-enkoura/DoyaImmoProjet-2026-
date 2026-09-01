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
        return (new MailMessage)
            ->subject(' Votre abonnement expire bientôt - DoyaImmo')
            ->markdown('emails.abonnement-expire', [
                'abonnement' => $this->abonnement,
                'joursRestants' => $this->joursRestants,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $abonnement = $this->abonnement;
        $jours = $this->joursRestants;

        $type = $jours <= 1 ? 'danger' : ($jours <= 3 ? 'warning' : 'info');

        return [
            'title' => ' Abonnement expire dans ' . $jours . ' jour' . ($jours > 1 ? 's' : ''),
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
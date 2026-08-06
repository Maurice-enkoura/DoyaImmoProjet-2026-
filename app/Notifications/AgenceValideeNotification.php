<?php

namespace App\Notifications;

use App\Models\Agence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgenceValideeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected Agence $agence)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ Votre agence DoyaImmo a été validée')
            ->greeting('Bonjour ' . $notifiable->prenom . ' ' . $notifiable->nom . ' !')
            ->line('Nous avons le plaisir de vous informer que votre agence **' . $this->agence->nom_agence . '** a été validée avec succès sur la plateforme DoyaImmo.')
            ->line('')
            ->line('Vous pouvez dès maintenant :')
            ->line('• 📝 Publier des biens immobiliers')
            ->line('• 💬 Répondre aux demandes des clients')
            ->line('• 📅 Gérer vos rendez-vous')
            ->line('• 📊 Suivre vos statistiques')
            ->line('')
            ->action('Accéder à votre espace agence', url('/agence/dashboard'))
            ->line('')
            ->line('Bienvenue dans la communauté DoyaImmo !')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Agence validée',
            'message' => 'Votre agence ' . $this->agence->nom_agence . ' a été validée avec succès.',
            'type' => 'success',
            'icon' => 'fa-check-circle',
            'link' => route('agence.dashboard'),
            'agence_id' => $this->agence->id,
            'agence_nom' => $this->agence->nom_agence,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
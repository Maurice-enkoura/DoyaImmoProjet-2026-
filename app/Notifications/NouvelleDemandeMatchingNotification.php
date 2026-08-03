<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NouvelleDemandeMatchingNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected DemandeImmobiliere $demande,
        protected $biensCorrespondants
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $bestMatch = $this->biensCorrespondants->first();
        
        $mail = (new MailMessage)
            ->subject('🔍 Nouvelle demande correspondante !')
            ->greeting('Bonjour ' . $notifiable->nom)
            ->line('Une nouvelle demande correspond à vos biens !')
            ->line('')
            ->line('📋 Détails de la demande :')
            ->line('• Type de bien: ' . $this->demande->type_bien->label())
            ->line('• Zone: ' . $this->demande->zone_recherchee)
            ->line('• Budget: ' . number_format($this->demande->budget_maximum, 0, ',', ' ') . ' F/mois')
            ->line('• Surface min: ' . ($this->demande->surface_minimum ?? 'Non spécifiée') . ' m²')
            ->line('• Chambres: ' . ($this->demande->nombre_chambres ?? 'Non spécifié'))
            ->line('')
            ->line('📊 Correspondance :')
            ->line('• ' . $this->biensCorrespondants->count() . ' biens correspondent')
            ->line('• Meilleur score: ' . $bestMatch['score'] . '% (' . $bestMatch['niveau'] . ')');

        return $mail
            ->action('Voir la demande', route('agence.demandes.show', $this->demande))
            ->line('Merci d\'utiliser DoyaImmo !');
    }

    public function toArray($notifiable): array
    {
        $bestMatch = $this->biensCorrespondants->first();
        
        return [
            'demande_id' => $this->demande->id,
            'type_bien' => $this->demande->type_bien->label(),
            'zone' => $this->demande->zone_recherchee,
            'budget' => $this->demande->budget_maximum,
            'biens_correspondants' => $this->biensCorrespondants->count(),
            'meilleur_score' => $bestMatch ? $bestMatch['score'] : 0,
            'meilleur_niveau' => $bestMatch ? $bestMatch['niveau'] : 'Aucun',
            'message' => 'Nouvelle demande correspondante !'
        ];
    }
}
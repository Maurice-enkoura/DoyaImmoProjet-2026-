<?php

namespace App\Notifications;

use App\Models\DemandeImmobiliere;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleDemandeCompatibleNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(protected DemandeImmobiliere $demande, protected int $score)
    {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $agence = $notifiable->agence;
        
        if (!$agence) {
            return (new MailMessage)
                ->subject('Nouvelle demande compatible')
                ->line('Une nouvelle demande compatible est disponible.')
                ->action('Voir la demande', url('/agence/demandes/' . $this->demande->id));
        }

        $typeBien = is_object($this->demande->type_bien) && method_exists($this->demande->type_bien, 'label') 
            ? $this->demande->type_bien->label() 
            : $this->demande->type_bien;

        $typeOperation = is_object($this->demande->type_operation) && method_exists($this->demande->type_operation, 'label') 
            ? $this->demande->type_operation->label() 
            : $this->demande->type_operation;

        $message = (new MailMessage)
            ->subject('🏠 Nouvelle demande compatible avec vos biens')
            ->greeting('Bonjour ' . $agence->nom_agence . ' !')
            ->line('Un nouveau besoin correspond à vos biens :')
            ->line('')
            ->line('**Type de bien :** ' . $typeBien)
            ->line('**Opération :** ' . $typeOperation)
            ->line('**Zone recherchée :** ' . $this->demande->zone_recherchee)
            ->line('**Budget :** ' . number_format($this->demande->budget_maximum, 0, ',', ' ') . ' FCFA')
            ->line('**Score de compatibilité :** ' . $this->score . '%');

        // Ajouter les détails du score si disponible
        if ($this->score >= 80) {
            $message->line('🏆 Excellente compatibilité !');
        } elseif ($this->score >= 60) {
            $message->line('👍 Bonne compatibilité');
        } elseif ($this->score >= 40) {
            $message->line('📊 Compatibilité moyenne');
        } else {
            $message->line('📈 Compatibilité à améliorer');
        }

        $message->action('Voir la demande', url('/agence/demandes/' . $this->demande->id))
                ->line('')
                ->line('Répondez rapidement pour maximiser vos chances !')
                ->salutation('L\'équipe DoyaImmo');

        return $message;
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Nouvelle demande compatible',
            'message' => 'Un nouveau besoin compatible avec vos biens est disponible.',
            'type' => 'info',
            'icon' => 'fa-bell',
            'link' => route('agence.demandes.show', $this->demande),
            'demande_id' => $this->demande->id,
            'score' => $this->score,
            'zone_recherchee' => $this->demande->zone_recherchee,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
<?php

namespace App\Notifications;

use App\Models\Proposition;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PropositionAccepteeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $proposition;

    public function __construct(Proposition $proposition)
    {
        $this->proposition = $proposition;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        $demande = $this->proposition->demande;
        $particulier = $demande->particulier->user;
        $agence = $this->proposition->agence;

        return (new MailMessage)
            ->subject('✅ Proposition acceptée - DoyaImmo')
            ->greeting('Bonjour ' . $agence->user->prenom . ' ' . $agence->user->nom . ' !')
            ->line('Nous avons le plaisir de vous informer que votre proposition a été **acceptée** par le client !')
            ->line('')
            ->line('**Détails de la proposition :**')
            ->line('• **Client :** ' . $particulier->prenom . ' ' . $particulier->nom)
            ->line('• **Email :** ' . $particulier->email)
            ->line('• **Téléphone :** ' . $particulier->telephone)
            ->line('• **Prix proposé :** ' . number_format($this->proposition->prix_propose, 0, ',', ' ') . ' FCFA')
            ->line('• **Bien :** ' . ($this->proposition->bien->titre ?? 'N/A'))
            ->line('')
            ->line('📞 Vous pouvez contacter le client pour organiser la suite.')
            ->action('Voir la proposition', url('/agence/propositions/' . $this->proposition->id))
            ->line('')
            ->line('Félicitations pour cette nouvelle collaboration !')
            ->salutation('L\'équipe DoyaImmo');
    }

    public function toDatabase($notifiable): array
    {
        $particulier = $this->proposition->demande->particulier->user;

        return [
            'title' => 'Proposition acceptée 🎉',
            'message' => 'Votre proposition pour ' . $particulier->prenom . ' ' . $particulier->nom . ' a été acceptée !',
            'type' => 'success',
            'icon' => 'fa-check-circle',
            'link' => route('agence.propositions.show', $this->proposition),
            'proposition_id' => $this->proposition->id,
            'particulier_nom' => $particulier->prenom . ' ' . $particulier->nom,
            'prix' => $this->proposition->prix_propose,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
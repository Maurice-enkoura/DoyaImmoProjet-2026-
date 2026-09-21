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
        return (new MailMessage)
            ->subject(' Proposition acceptée — DoyaImmo')
            ->view('emails.proposition-acceptee', [
                'proposition' => $this->proposition,
                'notifiable'  => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $particulier = $this->proposition->demande->particulier->user;

        return [
            'title'           => 'Proposition acceptée',
            'message'         => 'Votre proposition pour ' . $particulier->prenom . ' ' . $particulier->nom . ' a été acceptée !',
            'type'            => 'success',
            'icon'            => 'fa-check-circle',
            'link'            => route('agence.propositions.show', $this->proposition),
            'proposition_id'  => $this->proposition->id,
            'particulier_nom' => $particulier->prenom . ' ' . $particulier->nom,
            'prix'            => $this->proposition->prix_propose,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
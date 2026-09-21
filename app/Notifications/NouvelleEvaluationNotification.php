<?php

namespace App\Notifications;

use App\Models\Evaluation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NouvelleEvaluationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $evaluation;

    public function __construct(Evaluation $evaluation)
    {
        $this->evaluation = $evaluation;
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Nouvel avis reçu — DoyaImmo')
            ->view('emails.nouvelle-evaluation', [
                'evaluation' => $this->evaluation,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        $eval       = $this->evaluation;
        $particulier = $eval->particulier->user ?? null;
        $bien        = $eval->proposition->bien ?? null;

        return [
            'title'          => 'Nouvel avis reçu',
            'message'        => ($particulier ? $particulier->prenom : 'Un client') . ' a laissé un avis ' . $eval->note . '/5.',
            'type'           => $eval->note >= 4 ? 'success' : ($eval->note >= 3 ? 'warning' : 'danger'),
            'icon'           => 'fa-star',
            'link'           => route('agence.evaluations.show', $eval),
            'evaluation_id'  => $eval->id,
            'note'           => $eval->note,
            'bien_titre'     => $bien->titre ?? null,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
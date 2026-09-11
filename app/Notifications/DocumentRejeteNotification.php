<?php

namespace App\Notifications;

use App\Models\Agence;
use App\Models\DocumentAgence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentRejeteNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Agence $agence,
        protected DocumentAgence $document,
        protected ?string $motif = null
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject(' Document rejeté — DoyaImmo')
            ->markdown('emails.document-rejete', [
                'agence' => $this->agence,
                'document' => $this->document,
                'motif' => $this->motif,
                'user' => $notifiable,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => ' Document rejeté',
            'message' => 'Votre document "' . $this->document->type_document_label . '" a été rejeté.'
                . ($this->motif ? ' Motif : ' . $this->motif : ''),
            'type' => 'danger',
            'icon' => 'fa-circle-xmark',
            'link' => route('agence.profil', ['onglet' => 'documents']),
            'agence_id' => $this->agence->id,
            'agence_nom' => $this->agence->nom_agence,
            'document_id' => $this->document->id,
            'motif' => $this->motif,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
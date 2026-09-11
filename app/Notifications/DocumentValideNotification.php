<?php

namespace App\Notifications;

use App\Models\Agence;
use App\Models\DocumentAgence;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DocumentValideNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Agence $agence,
        protected DocumentAgence $document
    ) {
    }

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Document validé — DoyaImmo')
            ->markdown('emails.document-valide', [
                'agence' => $this->agence,
                'document' => $this->document,
                'user' => $notifiable,
                'notifiable' => $notifiable,
            ]);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'title' => 'Document validé',
            'message' => 'Votre document "' . $this->document->type_document_label . '" a été validé avec succès.',
            'type' => 'success',
            'icon' => 'fa-check-circle',
            'link' => route('agence.profil', ['onglet' => 'documents']),
            'agence_id' => $this->agence->id,
            'agence_nom' => $this->agence->nom_agence,
            'document_id' => $this->document->id,
        ];
    }

    public function toArray($notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
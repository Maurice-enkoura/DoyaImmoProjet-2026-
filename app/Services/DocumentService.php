<?php

namespace App\Services;

use App\Models\Agence;
use App\Models\DocumentAgence;
use App\Enums\TypeDocumentEnum;
use App\Enums\StatutDocumentEnum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class DocumentService
{
    public function uploadDocuments(Agence $agence, array $documents): void
    {
        foreach ($documents as $type => $file) {
            if ($file instanceof UploadedFile) {
                $path = $file->store('documents/agences/' . $agence->id, 'public');
                
                // Vérifier si le type est valide
                $typeEnum = TypeDocumentEnum::tryFrom($type);
                if (!$typeEnum) {
                    continue;
                }

                DocumentAgence::create([
                    'agence_id' => $agence->id,
                    'type_document' => $typeEnum->value,
                    'nom_fichier' => $path,
                    'statut_validation' => StatutDocumentEnum::EN_ATTENTE->value,
                ]);
            }
        }
    }

    public function getDocumentUrl(DocumentAgence $document): string
    {
        return Storage::disk('public')->url($document->nom_fichier);
    }

    public function deleteDocument(DocumentAgence $document): void
    {
        Storage::disk('public')->delete($document->nom_fichier);
        $document->delete();
    }
}
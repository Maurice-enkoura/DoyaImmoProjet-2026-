<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DocumentAgence;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Enums\TypeDocumentEnum;
use App\Enums\StatutDocumentEnum;

class ProfilController extends Controller
{
    public function __construct(private DocumentService $documentService) {}

    /**
     * Affiche le profil de l'agence
     */
    public function show()
    {
        $agence = Auth::user()->agence;
        $documents = $agence->documents()->with('validePar.user')->get();
        $documentTypes = TypeDocumentEnum::cases();
        $statutValidation = StatutDocumentEnum::labels();
        $estValidee = $agence->estValidee();
        $aAbonnement = $agence->aAbonnementActif();

        return view('agence.profil.index', compact(
            'agence', 
            'documents', 
            'documentTypes', 
            'statutValidation',
            'estValidee',
            'aAbonnement'
        ));
    }

    /**
     * Affiche le formulaire d'édition du profil
     */
    public function edit()
    {
        $agence = Auth::user()->agence;
        return view('agence.profil.edit', compact('agence'));
    }

    /**
     * Met à jour le profil de l'agence
     */
    public function update(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'nom_agence' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'quartier' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255',
            'site_web' => 'nullable|url|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        // Mettre à jour les informations de l'utilisateur
        $user = $agence->user;
        if ($request->filled('telephone')) {
            $user->telephone = $request->telephone;
        }
        if ($request->filled('email') && $request->email !== $user->email) {
            $request->validate(['email' => 'unique:users,email,' . $user->id]);
            $user->email = $request->email;
        }
        $user->save();

        // Mettre à jour les informations de l'agence
        $data = $request->only(['nom_agence', 'adresse', 'quartier', 'description']);
        
        // Gérer le logo
        if ($request->hasFile('logo')) {
            // Supprimer l'ancien logo
            if ($agence->logo && Storage::disk('public')->exists($agence->logo)) {
                Storage::disk('public')->delete($agence->logo);
            }
            
            $path = $request->file('logo')->store('logos/agences', 'public');
            $data['logo'] = $path;
        }

        $agence->update($data);

        return redirect()->route('agence.profil.show')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Télécharge un nouveau document pour l'agence
     */
    public function uploadDocument(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'type_document' => 'required|in:' . implode(',', array_column(TypeDocumentEnum::cases(), 'value')),
            'fichier' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        $type = TypeDocumentEnum::from($request->type_document);

        // Vérifier si un document de ce type existe déjà
        $existingDocument = $agence->documents()
            ->where('type_document', $type)
            ->first();

        if ($existingDocument) {
            // Supprimer l'ancien fichier
            if (Storage::disk('public')->exists($existingDocument->nom_fichier)) {
                Storage::disk('public')->delete($existingDocument->nom_fichier);
            }
            $existingDocument->delete();
        }

        $path = $request->file('fichier')->store('documents/agences/' . $agence->id, 'public');

        DocumentAgence::create([
            'agence_id' => $agence->id,
            'type_document' => $type,
            'nom_fichier' => $path,
            'statut_validation' => StatutDocumentEnum::EN_ATTENTE,
        ]);

        // Si l'agence était validée et qu'elle change un document, elle redevient en attente
        if ($agence->statut_validation) {
            $agence->update(['statut_validation' => false]);
        }

        return redirect()->route('agence.profil.show')
            ->with('success', 'Document téléchargé avec succès. En attente de validation.');
    }

    /**
     * Supprime un document de l'agence
     */
    public function deleteDocument(DocumentAgence $document)
    {
        $agence = Auth::user()->agence;

        if ($document->agence_id !== $agence->id) {
            abort(403);
        }

        if ($document->statut_validation === StatutDocumentEnum::VALIDE) {
            return back()->with('error', 'Impossible de supprimer un document déjà validé.');
        }

        // Supprimer le fichier
        if (Storage::disk('public')->exists($document->nom_fichier)) {
            Storage::disk('public')->delete($document->nom_fichier);
        }

        $document->delete();

        return redirect()->route('agence.profil.show')
            ->with('success', 'Document supprimé avec succès.');
    }

    /**
     * Télécharge un document de l'agence
     */
    public function downloadDocument(DocumentAgence $document)
    {
        $agence = Auth::user()->agence;

        if ($document->agence_id !== $agence->id) {
            abort(403);
        }

        if (!Storage::disk('public')->exists($document->nom_fichier)) {
            return back()->with('error', 'Fichier introuvable.');
        }

        return Storage::disk('public')->download($document->nom_fichier);
    }

    /**
     * Met à jour les informations de contact
     */
    public function updateContact(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'telephone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:users,email,' . $agence->user_id,
            'site_web' => 'nullable|url|max:255',
        ]);

        $user = $agence->user;
        if ($request->filled('telephone')) {
            $user->telephone = $request->telephone;
        }
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
        $user->save();

        return redirect()->route('agence.profil.show')
            ->with('success', 'Informations de contact mises à jour.');
    }

    /**
     * Met à jour la description de l'agence
     */
    public function updateDescription(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'description' => 'nullable|string|max:2000',
        ]);

        $agence->update(['description' => $request->description]);

        return redirect()->route('agence.profil.show')
            ->with('success', 'Description mise à jour.');
    }
}
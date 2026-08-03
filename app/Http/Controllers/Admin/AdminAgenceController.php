<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DocumentAgence;
use App\Models\User;
use App\Services\DocumentService;
use App\Notifications\AgenceValideeNotification;
use App\Notifications\AgenceRefuseeNotification;
use Illuminate\Http\Request;
use App\Enums\StatutDocumentEnum;
use Illuminate\Support\Facades\Auth;

class AdminAgenceController extends Controller
{
    public function __construct(private DocumentService $documentService) {}

    public function index()
    {
        $agences = Agence::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.agences.index', compact('agences'));
    }

    public function show(Agence $agence)
    {
        $agence->load(['user', 'documents', 'biens', 'evaluations.particulier.user']);
        return view('admin.agences.show', compact('agence'));
    }

    public function documents(Agence $agence)
    {
        $documents = $agence->documents()->with('validePar.user')->get();
        return view('admin.agences.documents', compact('agence', 'documents'));
    }

    public function validerDocuments(Request $request, Agence $agence)
    {
        $request->validate([
            'document_ids' => 'required|array',
            'document_ids.*' => 'exists:document_agences,id',
            'statut' => 'required|in:valide,rejete',
            'commentaire' => 'nullable|string|max:1000',
        ]);

        $statut = StatutDocumentEnum::from($request->statut);
        $adminId = Auth::user()->administrateur->id;

        foreach ($request->document_ids as $documentId) {
            $document = DocumentAgence::find($documentId);
            $document->update([
                'statut_validation' => $statut,
                'valide_par' => $adminId,
                'date_validation' => now(),
                'commentaire' => $request->commentaire,
            ]);
        }

        // Vérifier si tous les documents obligatoires sont validés
        $documentsObligatoires = $agence->documents()
            ->where('type_document', '!=', 'logo')
            ->where('statut_validation', StatutDocumentEnum::VALIDE)
            ->count();

        $totalObligatoires = $agence->documents()
            ->where('type_document', '!=', 'logo')
            ->count();

        if ($documentsObligatoires === $totalObligatoires && $totalObligatoires > 0) {
            $agence->update(['statut_validation' => true]);
            $agence->user->notify(new AgenceValideeNotification($agence));
        } elseif ($statut === StatutDocumentEnum::REJETE) {
            $agence->user->notify(new AgenceRefuseeNotification($agence, $request->commentaire));
        }

        return redirect()->back()->with('success', 'Documents traités avec succès.');
    }

    public function valider(Agence $agence)
    {
        $agence->update(['statut_validation' => true]);
        $agence->user->notify(new AgenceValideeNotification($agence));

        return redirect()->route('admin.agences')->with('success', 'Agence validée avec succès.');
    }

    public function refuser(Request $request, Agence $agence)
    {
        $request->validate([
            'motif' => 'required|string|max:1000',
        ]);

        $agence->update(['statut_validation' => false]);
        $agence->user->notify(new AgenceRefuseeNotification($agence, $request->motif));

        return redirect()->route('admin.agences')->with('success', 'Agence refusée.');
    }

    public function toggleStatut(Agence $agence)
    {
        $agence->update(['statut_validation' => !$agence->statut_validation]);

        if ($agence->statut_validation) {
            $agence->user->notify(new AgenceValideeNotification($agence));
        }

        return redirect()->back()->with('success', 'Statut de l\'agence mis à jour.');
    }

    public function destroy(Agence $agence)
    {
        $agence->delete();
        return redirect()->route('admin.agences')->with('success', 'Agence supprimée avec succès.');
    }
}
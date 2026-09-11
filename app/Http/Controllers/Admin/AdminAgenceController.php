<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DocumentAgence;
use App\Models\User;
use App\Services\DocumentService;
use App\Notifications\AgenceValideeNotification;
use App\Notifications\AgenceRefuseeNotification;
use App\Notifications\DocumentValideNotification;
use App\Notifications\DocumentRejeteNotification;
use Illuminate\Http\Request;
use App\Enums\StatutDocumentEnum;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminAgenceController extends Controller
{
    public function __construct(private DocumentService $documentService) {}

    public function index(Request $request)
    {
        $query = Agence::with(['user', 'quartier', 'documents', 'biens']);

        if ($request->filled('filtre')) {
            switch ($request->filtre) {
                case 'en_attente':
                    $query->where('statut_validation', false)->where('est_refusee', false);
                    break;
                case 'refusees':
                    $query->where('est_refusee', true);
                    break;
                case 'validees':
                    $query->where('statut_validation', true);
                    if (Schema::hasColumn('agences', 'bloque')) {
                        $query->where('bloque', false);
                    }
                    break;
                case 'bloquees':
                    if (Schema::hasColumn('agences', 'bloque')) {
                        $query->where('bloque', true);
                    } else {
                        $query->whereRaw('1 = 0');
                    }
                    break;
            }
        }

        $agences = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.agences.index', compact('agences'));
    }

    /**
     * Affiche le détail d'une agence - UTILISE LE SLUG
     */
    public function show(Agence $agence)
    {
        $agence->load([
            'user', 
            'documents', 
            'biens.medias', 
            'quartier'
        ]);
        return view('admin.agences.show', compact('agence'));
    }

    /**
     * Affiche les documents d'une agence - UTILISE LE SLUG
     */
    public function documents(Agence $agence)
    {
        $documents = $agence->documents()->with('validePar.user')->get();
        return view('admin.agences.documents', compact('agence', 'documents'));
    }

    /**
     * Valide ou rejette les documents d'une agence - UTILISE LE SLUG
     */
   public function validerDocuments(Request $request, Agence $agence)
{
    $request->validate([
        'document_ids' => 'required|array',
        'document_ids.*' => 'exists:document_agences,id',
        'statut' => 'required|in:valide,rejete',
        'commentaire' => 'nullable|string|max:1000',
    ]);

    $statut = StatutDocumentEnum::from($request->statut);
    $adminId = Auth::user()->administrateur->id ?? null;

    DB::beginTransaction();

    try {
        foreach ($request->document_ids as $documentId) {
            $document = DocumentAgence::find($documentId);
            
            // ✅ Vérifier que le document appartient à l'agence
            if ($document->agence_id !== $agence->id) {
                continue;
            }

            $document->update([
                'statut_validation' => $statut->value,
                'valide_par' => $adminId,
                'date_validation' => now(),
                'commentaire' => $request->commentaire,
            ]);

            $user = $agence->user;
            
            // ✅ TOUJOURS envoyer la notification, même sans commentaire
            if ($statut === StatutDocumentEnum::VALIDE) {
                $user->notify(new DocumentValideNotification($agence, $document));
            } elseif ($statut === StatutDocumentEnum::REJETE) {
                // ✅ On passe le commentaire (peut être null)
                $user->notify(new DocumentRejeteNotification($agence, $document, $request->commentaire));
            }
        }

        // ✅ Vérifier si TOUS les documents obligatoires sont validés
        $documentsObligatoires = $agence->documents()
            ->where('type_document', '!=', 'logo')
            ->where('statut_validation', StatutDocumentEnum::VALIDE->value)
            ->count();

        $totalObligatoires = $agence->documents()
            ->where('type_document', '!=', 'logo')
            ->count();

        if ($statut === StatutDocumentEnum::REJETE) {
            // ❌ Si un document est rejeté, l'agence NE DOIT PAS être validée
            $agence->update([
                'statut_validation' => false,
                'est_refusee' => false,
                'motif_refus' => null,
                'date_refus' => null,
            ]);
        } elseif ($documentsObligatoires === $totalObligatoires && $totalObligatoires > 0) {
            // ✅ Si tous les documents sont validés, valider l'agence
            $agence->update(['statut_validation' => true]);
            $agence->user->notify(new AgenceValideeNotification($agence));
        }

        DB::commit();

        $message = $statut === StatutDocumentEnum::VALIDE 
            ? ' Documents validés avec succès.'
            : 'Documents rejetés avec succès. L\'agence a été informée et ne pourra pas être validée.';

        return redirect()->route('admin.agences.documents', ['agence' => $agence->slug])
            ->with('success', $message);

    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->route('admin.agences.documents', ['agence' => $agence->slug])
            ->with('error', 'Une erreur est survenue: ' . $e->getMessage());
    }
}
    /**
     * Rejette un document spécifique - UTILISE LE SLUG
     */
    public function rejeterDocument(Request $request, Agence $agence, DocumentAgence $document)
{
    // ✅ Vérifier que le document appartient bien à l'agence
    if ($document->agence_id !== $agence->id) {
        abort(403, 'Ce document n\'appartient pas à cette agence.');
    }

    // ✅ Validation des données
    $request->validate([
        'motif' => 'required|string|max:1000',
    ]);

    $adminId = Auth::user()->administrateur->id ?? null;

    DB::beginTransaction();

    try {
        // ✅ Mettre à jour le document avec le statut rejeté
        $document->update([
            'statut_validation' => StatutDocumentEnum::REJETE->value,
            'valide_par' => $adminId,
            'date_validation' => now(),
            'commentaire' => $request->motif,
        ]);

        // ✅ TOUJOURS envoyer la notification
        $agence->user->notify(new DocumentRejeteNotification(
            $agence, 
            $document, 
            $request->motif
        ));

        DB::commit();

        return redirect()
            ->route('admin.agences.documents', ['agence' => $agence->slug])
            ->with('success', 'Document "' . $document->type_document_label . '" rejeté avec succès.');

    } catch (\Exception $e) {
        DB::rollBack();
        
        return redirect()
            ->route('admin.agences.documents', ['agence' => $agence->slug])
            ->with('error', 'Une erreur est survenue lors du rejet: ' . $e->getMessage());
    }
}

    /**
     * Valide une agence - UTILISE LE SLUG
     */
    public function valider(Agence $agence)
    {
        try {
            // ✅ Vérifier que tous les documents obligatoires sont validés
            $documentsObligatoires = $agence->documents()
                ->where('type_document', '!=', 'logo')
                ->where('statut_validation', StatutDocumentEnum::VALIDE->value)
                ->count();

            $totalObligatoires = $agence->documents()
                ->where('type_document', '!=', 'logo')
                ->count();

            if ($documentsObligatoires !== $totalObligatoires || $totalObligatoires === 0) {
                return redirect()->route('admin.agences.index')
                    ->with('error', 'Impossible de valider l\'agence : tous les documents obligatoires doivent être validés.');
            }

            // Si l'agence était refusée, la remettre en attente avant de valider
            if ($agence->est_refusee) {
                $agence->update([
                    'est_refusee' => false,
                    'motif_refus' => null,
                    'date_refus' => null,
                ]);
            }
            
            $agence->update(['statut_validation' => true]);
            $agence->user->notify(new AgenceValideeNotification($agence));

            return redirect()->route('admin.agences.index')
                ->with('success', 'Agence validée avec succès. Un email a été envoyé.');

        } catch (\Exception $e) {
            Log::error('Erreur validation agence: ' . $e->getMessage());
            
            return redirect()->route('admin.agences.index')
                ->with('error', 'Erreur lors de la validation: ' . $e->getMessage());
        }
    }

    /**
     * Refuse une agence - UTILISE LE SLUG
     */
    public function refuser(Request $request, Agence $agence)
    {
        $request->validate([
            'motif' => 'required|string|max:1000',
        ]);

        try {
            // ✅ Marquer l'agence comme refusée
            $agence->update([
                'statut_validation' => false,
                'est_refusee' => true,
                'motif_refus' => $request->motif,
                'date_refus' => now(),
            ]);
            
            $agence->user->notify(new AgenceRefuseeNotification($agence, $request->motif));

            return redirect()->route('admin.agences.index')
                ->with('success', 'Agence refusée. Un email a été envoyé avec le motif du refus.');

        } catch (\Exception $e) {
            Log::error('Erreur refus agence: ' . $e->getMessage());
            
            return redirect()->route('admin.agences.index')
                ->with('error', 'Erreur lors du refus: ' . $e->getMessage());
        }
    }

    /**
     * ✅ Réactiver une agence refusée - UTILISE LE SLUG
     */
    public function reactiver(Agence $agence)
    {
        try {
            $agence->update([
                'statut_validation' => false,
                'est_refusee' => false,
                'motif_refus' => null,
                'date_refus' => null,
            ]);

            return redirect()->route('admin.agences.index')
                ->with('success', 'Agence réactivée avec succès. Elle est maintenant en attente de validation.');

        } catch (\Exception $e) {
            Log::error('Erreur réactivation agence: ' . $e->getMessage());
            
            return redirect()->route('admin.agences.index')
                ->with('error', 'Erreur lors de la réactivation: ' . $e->getMessage());
        }
    }

    /**
     * Bloque une agence - UTILISE LE SLUG
     */
    public function bloquer(Agence $agence)
    {
        if (Schema::hasColumn('agences', 'bloque')) {
            $agence->update(['bloque' => true]);
            return redirect()->route('admin.agences.index')
                ->with('success', 'Agence bloquée.');
        }
        
        return redirect()->route('admin.agences.index')
            ->with('error', 'La fonction de blocage n\'est pas disponible.');
    }

    /**
     * Débloque une agence - UTILISE LE SLUG
     */
    public function debloquer(Agence $agence)
    {
        if (Schema::hasColumn('agences', 'bloque')) {
            $agence->update(['bloque' => false]);
            return redirect()->route('admin.agences.index')
                ->with('success', 'Agence débloquée.');
        }
        
        return redirect()->route('admin.agences.index')
            ->with('error', 'La fonction de déblocage n\'est pas disponible.');
    }

    /**
     * Supprime une agence - UTILISE LE SLUG
     */
    public function destroy(Agence $agence)
    {
        $user = $agence->user;
        $agence->delete();
        $user->delete();

        return redirect()->route('admin.agences.index')
            ->with('success', 'Agence supprimée avec succès.');
    }
}
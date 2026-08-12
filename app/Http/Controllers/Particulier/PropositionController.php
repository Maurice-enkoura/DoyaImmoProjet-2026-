<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\Models\DemandeImmobiliere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutDemandeEnum;

class PropositionController extends Controller
{
    public function index()
    {
        // ✅ Récupérer les propositions en attente des demandes actives
        // ET les propositions acceptées
        $propositions = Proposition::with(['demande.particulier.user', 'agence.user', 'bien'])
            ->where('particulier_id', Auth::user()->particulier->id)
            ->whereHas('demande', function($query) {
                // Filtrer par demandes actives (en_attente ou en_cours)
                $query->whereIn('statut', [
                    StatutDemandeEnum::EN_ATTENTE->value,
                    StatutDemandeEnum::EN_COURS->value
                ]);
            })
            ->where(function($query) {
                // ✅ Soit la proposition est en attente ET la demande n'a PAS de proposition acceptée
                $query->where('statut', StatutPropositionEnum::EN_ATTENTE->value)
                      ->whereDoesntHave('demande.propositions', function($subQ) {
                          $subQ->where('statut', StatutPropositionEnum::ACCEPTEE->value);
                      })
                      // ✅ Soit la proposition est acceptée
                      ->orWhere('statut', StatutPropositionEnum::ACCEPTEE->value);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('particulier.propositions.index', compact('propositions'));
    }

    public function show(Proposition $proposition)
    {
        $proposition->load(['demande.particulier.user', 'agence.user', 'bien.medias', 'medias']);
        return view('particulier.propositions.show', compact('proposition'));
    }

    public function comparer(Request $request)
    {
        $demandeId = $request->demande_id;
        $demande = DemandeImmobiliere::with('particulier.user')->findOrFail($demandeId);

        $propositions = Proposition::with(['agence.user', 'bien'])
            ->where('demande_id', $demandeId)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value
            ])
            ->orderBy('prix_propose', 'asc')
            ->get();

        return view('particulier.propositions.comparer', compact('propositions', 'demande'));
    }

    public function selectionner(Proposition $proposition)
    {
        if ($proposition->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        if ($proposition->statut !== StatutPropositionEnum::EN_ATTENTE) {
            return back()->with('error', 'Cette proposition ne peut plus être sélectionnée.');
        }

        // ✅ Vérifier que la demande est encore active
        $demande = $proposition->demande;
        
        // ✅ Récupérer la valeur du statut de la demande (UNIQUEMENT avec value)
        $statutDemande = $demande->statut->value;
        
        if (!in_array($statutDemande, [
            StatutDemandeEnum::EN_ATTENTE->value,
            StatutDemandeEnum::EN_COURS->value
        ])) {
            return back()->with('error', 'Cette demande n\'est plus active. Statut actuel: ' . $statutDemande);
        }

        // ✅ Vérifier qu'aucune proposition n'est déjà acceptée
        $hasAcceptee = $demande->propositions()
            ->where('statut', StatutPropositionEnum::ACCEPTEE->value)
            ->exists();
            
        if ($hasAcceptee) {
            return back()->with('error', 'Une proposition a déjà été acceptée pour cette demande.');
        }

        // Mettre à jour le statut de la proposition sélectionnée
        $proposition->update(['statut' => StatutPropositionEnum::ACCEPTEE]);

        // Refuser TOUTES les autres propositions de cette demande
        Proposition::where('demande_id', $proposition->demande_id)
            ->where('id', '!=', $proposition->id)
            ->whereIn('statut', [
                StatutPropositionEnum::EN_ATTENTE->value,
                StatutPropositionEnum::ACCEPTEE->value
            ])
            ->update(['statut' => StatutPropositionEnum::REFUSEE]);

        // Mettre à jour le statut de la demande
        $demande->update(['statut' => StatutDemandeEnum::EN_COURS->value]);

        // Notifier l'agence
        try {
            $proposition->agence->user->notify(new \App\Notifications\PropositionAccepteeNotification($proposition));
        } catch (\Exception $e) {
            \Log::error('Erreur notification: ' . $e->getMessage());
        }

        return redirect()->route('particulier.propositions.index')
            ->with('success', 'Proposition sélectionnée avec succès. Les autres propositions ont été refusées.');
    }
}
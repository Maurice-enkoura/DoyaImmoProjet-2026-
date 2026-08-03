<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\Models\DemandeImmobiliere;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutPropositionEnum;

class PropositionController extends Controller
{
    public function index()
    {
        $propositions = Proposition::with(['demande.particulier.user', 'agence.user', 'bien'])
            ->where('particulier_id', Auth::user()->particulier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('particulier.propositions.index', compact('propositions'));
    }

    public function show(Proposition $proposition)
    {
        $proposition->load(['demande.particulier.user', 'agence.user', 'bien.medias']);
        return view('particulier.propositions.show', compact('proposition'));
    }

    public function comparer(Request $request)
    {
        $demandeId = $request->demande_id;
        $demande = DemandeImmobiliere::with('particulier.user')->findOrFail($demandeId);

        $propositions = Proposition::with(['agence.user', 'bien'])
            ->where('demande_id', $demandeId)
            ->where('statut', '!=', StatutPropositionEnum::REFUSEE)
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

        // Mettre à jour le statut de la proposition
        $proposition->update(['statut' => StatutPropositionEnum::ACCEPTEE]);

        // Refuser les autres propositions
        Proposition::where('demande_id', $proposition->demande_id)
            ->where('id', '!=', $proposition->id)
            ->where('statut', StatutPropositionEnum::EN_ATTENTE)
            ->update(['statut' => StatutPropositionEnum::REFUSEE]);

        // Mettre à jour le statut de la demande
        $proposition->demande->update(['statut' => 'en_cours']);

        // Notifier l'agence
        $proposition->agence->user->notify(new \App\Notifications\PropositionAccepteeNotification($proposition));

        return redirect()->route('particulier.propositions.index')
            ->with('success', 'Proposition sélectionnée avec succès.');
    }
}
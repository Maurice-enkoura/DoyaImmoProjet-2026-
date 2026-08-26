<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationRequest;
use App\Models\Evaluation;
use App\Models\Agence;
use App\Models\RendezVous;
use App\Models\Proposition;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with(['particulier.user', 'agence.user'])
            ->where('particulier_id', Auth::user()->particulier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('particulier.evaluations.index', compact('evaluations'));
    }

    public function create(Agence $agence)
    {
        $particulierId = Auth::user()->particulier->id;

        // ✅ Vérifier que le particulier a eu un rendez-vous terminé avec cette agence
        $aEuRendezVous = RendezVous::where('particulier_id', $particulierId)
            ->where('agence_id', $agence->id)
            ->where('statut', StatutRendezVousEnum::TERMINE->value)
            ->exists();

        if (!$aEuRendezVous) {
            return redirect()->route('particulier.rendezvous.index')
                ->with('error', 'Vous ne pouvez évaluer que les agences avec lesquelles vous avez eu un rendez-vous terminé.');
        }

        // ✅ Vérifier que le particulier n'a pas déjà évalué cette agence
        $dejaEvalue = Evaluation::where('particulier_id', $particulierId)
            ->where('agence_id', $agence->id)
            ->exists();

        if ($dejaEvalue) {
            // ✅ Récupérer l'évaluation existante pour rediriger vers celle-ci
            $evaluationExistante = Evaluation::where('particulier_id', $particulierId)
                ->where('agence_id', $agence->id)
                ->first();
                
            return redirect()->route('particulier.evaluations.show', $evaluationExistante)
                ->with('info', 'Vous avez déjà évalué cette agence. Voici votre évaluation.');
        }

        return view('particulier.evaluations.create', compact('agence'));
    }

    public function store(EvaluationRequest $request)
    {
        $particulierId = Auth::user()->particulier->id;

        // ✅ Vérifier qu'il n'y a pas d'évaluation existante avant de créer
        $existe = Evaluation::where('particulier_id', $particulierId)
            ->where('agence_id', $request->agence_id)
            ->exists();

        if ($existe) {
            return redirect()->route('particulier.evaluations.index')
                ->with('error', 'Vous avez déjà évalué cette agence.');
        }

        Evaluation::create([
            'particulier_id' => $particulierId,
            'agence_id' => $request->agence_id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
        ]);

        return redirect()->route('particulier.evaluations.index')
            ->with('success', 'Évaluation envoyée avec succès.');
    }

    public function show(Evaluation $evaluation)
    {
        if ($evaluation->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $evaluation->load(['particulier.user', 'agence.user']);
        return view('particulier.evaluations.show', compact('evaluation'));
    }

    /**
     * Affiche le formulaire d'édition d'une évaluation
     */
    public function edit(Evaluation $evaluation)
    {
        if ($evaluation->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        return view('particulier.evaluations.edit', compact('evaluation'));
    }

    /**
     * Met à jour une évaluation
     */
    public function update(EvaluationRequest $request, Evaluation $evaluation)
    {
        if ($evaluation->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $evaluation->update([
            'note' => $request->note,
            'commentaire' => $request->commentaire,
        ]);

        return redirect()->route('particulier.evaluations.show', $evaluation)
            ->with('success', 'Évaluation mise à jour avec succès.');
    }

    /**
     * Supprime une évaluation
     */
    public function destroy(Evaluation $evaluation)
    {
        if ($evaluation->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $evaluation->delete();

        return redirect()->route('particulier.evaluations.index')
            ->with('success', 'Évaluation supprimée avec succès.');
    }
}
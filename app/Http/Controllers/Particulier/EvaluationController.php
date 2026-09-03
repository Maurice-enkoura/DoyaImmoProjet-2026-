<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationRequest;
use App\Models\Evaluation;
use App\Models\Agence;
use App\Models\Proposition;
use App\Enums\StatutPropositionEnum;
use Illuminate\Support\Facades\Auth;

class EvaluationController extends Controller
{
    public function index()
    {
        $evaluations = Evaluation::with(['particulier.user', 'agence.user', 'proposition.bien'])
            ->where('particulier_id', Auth::user()->particulier->id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('particulier.evaluations.index', compact('evaluations'));
    }

    /**
     * Créer une évaluation pour une proposition terminée
     */
    public function create(Proposition $proposition)
    {
        $particulier = Auth::user()->particulier;

        // ✅ Vérifier que la proposition appartient au particulier
        if ($proposition->particulier_id !== $particulier->id) {
            abort(403, 'Cette proposition ne vous appartient pas.');
        }

        // ✅ Vérifier que la proposition est terminée
        if ($proposition->statut !== StatutPropositionEnum::TERMINEE->value) {
            return redirect()->route('particulier.propositions.index')
                ->with('error', 'Vous ne pouvez évaluer que les propositions terminées.');
        }

        // ✅ Vérifier que le particulier n'a pas déjà évalué cette proposition
        $evaluationExistante = Evaluation::where('particulier_id', $particulier->id)
            ->where('proposition_id', $proposition->id)
            ->first();

        if ($evaluationExistante) {
            return redirect()->route('particulier.evaluations.show', $evaluationExistante)
                ->with('info', 'Vous avez déjà évalué cette proposition.');
        }

        $agence = $proposition->agence;

        return view('particulier.evaluations.create', compact('proposition', 'agence'));
    }

    /**
     * Stocker une nouvelle évaluation
     */
    public function store(EvaluationRequest $request)
    {
        $particulier = Auth::user()->particulier;

        // ✅ Vérifier la proposition
        $proposition = Proposition::findOrFail($request->proposition_id);

        // ✅ Vérifier que la proposition appartient au particulier
        if ($proposition->particulier_id !== $particulier->id) {
            abort(403, 'Cette proposition ne vous appartient pas.');
        }

        // ✅ Vérifier que la proposition est terminée
        if ($proposition->statut !== StatutPropositionEnum::TERMINEE->value) {
            return redirect()->route('particulier.propositions.index')
                ->with('error', 'Vous ne pouvez évaluer que les propositions terminées.');
        }

        // ✅ Vérifier qu'il n'y a pas d'évaluation existante pour cette proposition
        $existe = Evaluation::where('particulier_id', $particulier->id)
            ->where('proposition_id', $proposition->id)
            ->exists();

        if ($existe) {
            return redirect()->route('particulier.evaluations.index')
                ->with('error', 'Vous avez déjà évalué cette proposition.');
        }

        // ✅ Créer l'évaluation
        $evaluation = Evaluation::create([
            'particulier_id' => $particulier->id,
            'agence_id' => $proposition->agence_id,
            'proposition_id' => $proposition->id,
            'note' => $request->note,
            'commentaire' => $request->commentaire,
            'date_evaluation' => now(),
        ]);

        return redirect()->route('particulier.evaluations.index')
            ->with('success', 'Évaluation envoyée avec succès.');
    }

    /**
     * Afficher une évaluation
     */
    public function show(Evaluation $evaluation)
    {
        if ($evaluation->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        $evaluation->load(['particulier.user', 'agence.user', 'proposition.bien']);
        return view('particulier.evaluations.show', compact('evaluation'));
    }

    /**
     * Modifier une évaluation
     */
    public function edit(Evaluation $evaluation)
    {
        if ($evaluation->particulier_id !== Auth::user()->particulier->id) {
            abort(403);
        }

        return view('particulier.evaluations.edit', compact('evaluation'));
    }

    /**
     * Mettre à jour une évaluation
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
     * Supprimer une évaluation
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

    /**
     * Liste des propositions terminées non évaluées
     */
    public function propositionsEvaluables()
    {
        $particulier = Auth::user()->particulier;

        $propositions = Proposition::where('particulier_id', $particulier->id)
            ->where('statut', StatutPropositionEnum::TERMINEE->value)
            ->whereDoesntHave('evaluation', function($query) use ($particulier) {
                $query->where('particulier_id', $particulier->id);
            })
            ->with(['agence', 'bien'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.evaluations.propositions', compact('propositions'));
    }
}
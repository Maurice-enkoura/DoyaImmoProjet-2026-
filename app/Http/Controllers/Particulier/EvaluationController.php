<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Http\Requests\EvaluationRequest;
use App\Models\Evaluation;
use App\Models\Agence;
use App\Models\RendezVous;
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

        // Vérifier que le particulier a eu un rendez-vous terminé avec cette agence
        $aEuRendezVous = RendezVous::where('particulier_id', $particulierId)
            ->where('agence_id', $agence->id)
            ->where('statut', 'termine')
            ->exists();

        if (!$aEuRendezVous) {
            return back()->with('error', 'Vous ne pouvez évaluer que les agences avec lesquelles vous avez eu un rendez-vous terminé.');
        }

        // Vérifier que le particulier n'a pas déjà évalué cette agence
        $dejaEvalue = Evaluation::where('particulier_id', $particulierId)
            ->where('agence_id', $agence->id)
            ->exists();

        if ($dejaEvalue) {
            return back()->with('error', 'Vous avez déjà évalué cette agence.');
        }

        return view('particulier.evaluations.create', compact('agence'));
    }

    public function store(EvaluationRequest $request)
    {
        Evaluation::create([
            'particulier_id' => Auth::user()->particulier->id,
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
}
<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Http\Requests\SignalementRequest;
use App\Models\Signalement;
use App\Models\BienImmobilier;
use App\Models\Proposition;
use App\Models\DemandeImmobiliere;
use App\Models\Particulier;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutSignalementEnum;
use App\Enums\MotifSignalementEnum;

class SignalementController extends Controller
{
    public function createProposition(Proposition $proposition)
    {
        // Vérifier que la proposition appartient bien au particulier
        if ($proposition->particulier_id !== Auth::user()->particulier->id) {
            abort(403, 'Vous n\'êtes pas autorisé à signaler cette proposition.');
        }

        $motifs = MotifSignalementEnum::labels();
        return view('particulier.signalements.create-proposition', compact('proposition', 'motifs'));
    }

    public function createBien(BienImmobilier $bien)
    {
        $motifs = MotifSignalementEnum::labels();
        return view('particulier.signalements.create-bien', compact('bien', 'motifs'));
    }

    public function createDemande(DemandeImmobiliere $demande)
    {
        $motifs = MotifSignalementEnum::labels();
        return view('particulier.signalements.create-demande', compact('demande', 'motifs'));
    }

    public function store(SignalementRequest $request)
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();

        // Vérifier que l'utilisateur n'a pas déjà signalé cet élément
        $existe = Signalement::where('particulier_id', $particulier->id)
            ->where('signalable_id', $request->signalable_id)
            ->where('signalable_type', $request->signalable_type)
            ->where('statut', '!=', StatutSignalementEnum::REJETE)
            ->exists();

        if ($existe) {
            return back()->with('error', 'Vous avez déjà signalé cet élément.');
        }

        Signalement::create([
            'particulier_id' => $particulier->id,
            'signalable_id' => $request->signalable_id,
            'signalable_type' => $request->signalable_type,
            'motif' => $request->motif,
            'description' => $request->description,
            'statut' => StatutSignalementEnum::EN_ATTENTE,
        ]);

        return redirect()->route('particulier.signalements.mes')
            ->with('success', 'Votre signalement a été envoyé. Un administrateur va le traiter.');
    }

    public function mesSignalements()
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();
        
        $signalements = Signalement::where('particulier_id', $particulier->id)
            ->with('signalable')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('particulier.signalements.mes-signalements', compact('signalements'));
    }
}
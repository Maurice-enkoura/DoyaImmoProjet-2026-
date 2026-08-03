<?php

namespace App\Http\Controllers;

use App\Http\Requests\SignalementRequest;
use App\Models\Signalement;
use App\Models\BienImmobilier;
use App\Models\Proposition;
use App\Models\DemandeImmobiliere;
use App\Models\Particulier;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutSignalementEnum;

class SignalementController extends Controller
{
    public function index()
    {
        $signalements = Signalement::with(['particulier.user', 'signalable'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('signalements.index', compact('signalements'));
    }

    public function createBien(BienImmobilier $bien)
    {
        return view('signalements.create-bien', compact('bien'));
    }

    public function createProposition(Proposition $proposition)
    {
        return view('signalements.create-proposition', compact('proposition'));
    }

    public function createDemande(DemandeImmobiliere $demande)
    {
        return view('signalements.create-demande', compact('demande'));
    }

    public function store(SignalementRequest $request)
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();

        Signalement::create([
            'particulier_id' => $particulier->id,
            'signalable_id' => $request->signalable_id,
            'signalable_type' => $request->signalable_type,
            'motif' => $request->motif,
            'description' => $request->description,
            'statut' => StatutSignalementEnum::EN_ATTENTE,
        ]);

        return redirect()->route('signalements.mes-signalements')
            ->with('success', 'Signalement envoyé avec succès. Un administrateur va le traiter.');
    }

    public function show(Signalement $signalement)
    {
        $signalement->load(['particulier.user', 'signalable']);

        if (Auth::user()->isAdmin() || Auth::id() === $signalement->particulier->user_id) {
            return view('signalements.show', compact('signalement'));
        }

        abort(403);
    }

    public function mesSignalements()
    {
        $particulier = Particulier::where('user_id', Auth::id())->first();
        
        $signalements = Signalement::where('particulier_id', $particulier->id)
            ->with('signalable')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('signalements.mes-signalements', compact('signalements'));
    }

    public function adminIndex()
    {
        $this->authorize('viewAny', Signalement::class);

        $signalements = Signalement::with(['particulier.user', 'signalable'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.signalements.index', compact('signalements'));
    }

    public function adminShow(Signalement $signalement)
    {
        $this->authorize('view', $signalement);

        $signalement->load(['particulier.user', 'signalable']);
        return view('admin.signalements.show', compact('signalement'));
    }

    public function traiter(Signalement $signalement)
    {
        $this->authorize('update', $signalement);

        $signalement->update([
            'statut' => StatutSignalementEnum::TRAITE,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.signalements')
            ->with('success', 'Signalement traité avec succès.');
    }

    public function rejeter(Signalement $signalement)
    {
        $this->authorize('update', $signalement);

        $signalement->update([
            'statut' => StatutSignalementEnum::REJETE,
            'date_traitement' => now(),
        ]);

        return redirect()->route('admin.signalements')
            ->with('success', 'Signalement rejeté.');
    }
}
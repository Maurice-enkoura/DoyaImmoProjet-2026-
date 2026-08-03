<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Abonnement;
use App\Models\Agence;
use App\Enums\FormuleAbonnementEnum;
use Illuminate\Http\Request;

class AdminAbonnementController extends Controller
{
    public function index()
    {
        $abonnements = Abonnement::with('agence.user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        $stats = [
            'total' => Abonnement::count(),
            'actifs' => Abonnement::where('statut', true)->where('date_fin', '>', now())->count(),
            'expires' => Abonnement::where('statut', false)->orWhere('date_fin', '<=', now())->count(),
            'par_formule' => Abonnement::selectRaw('formule, count(*) as total')
                ->groupBy('formule')
                ->get(),
        ];

        return view('admin.abonnements.index', compact('abonnements', 'stats'));
    }

    public function show(Abonnement $abonnement)
    {
        $abonnement->load('agence.user');
        return view('admin.abonnements.show', compact('abonnement'));
    }

    public function create()
    {
        $agences = Agence::with('user')->get();
        $formules = FormuleAbonnementEnum::cases();
        return view('admin.abonnements.create', compact('agences', 'formules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'agence_id' => 'required|exists:agences,id',
            'formule' => 'required|in:' . implode(',', array_column(FormuleAbonnementEnum::cases(), 'value')),
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'statut' => 'boolean',
        ]);

        $formule = FormuleAbonnementEnum::from($request->formule);

        // Désactiver les anciens abonnements
        Abonnement::where('agence_id', $request->agence_id)
            ->where('statut', true)
            ->update(['statut' => false]);

        Abonnement::create([
            'agence_id' => $request->agence_id,
            'formule' => $request->formule,
            'montant' => $formule->prix(),
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->boolean('statut'),
        ]);

        return redirect()->route('admin.abonnements')->with('success', 'Abonnement créé avec succès.');
    }

    public function edit(Abonnement $abonnement)
    {
        $formules = FormuleAbonnementEnum::cases();
        return view('admin.abonnements.edit', compact('abonnement', 'formules'));
    }

    public function update(Request $request, Abonnement $abonnement)
    {
        $request->validate([
            'formule' => 'required|in:' . implode(',', array_column(FormuleAbonnementEnum::cases(), 'value')),
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after:date_debut',
            'statut' => 'boolean',
        ]);

        $formule = FormuleAbonnementEnum::from($request->formule);

        $abonnement->update([
            'formule' => $request->formule,
            'montant' => $formule->prix(),
            'date_debut' => $request->date_debut,
            'date_fin' => $request->date_fin,
            'statut' => $request->boolean('statut'),
        ]);

        return redirect()->route('admin.abonnements')->with('success', 'Abonnement mis à jour.');
    }

    public function destroy(Abonnement $abonnement)
    {
        $abonnement->delete();
        return redirect()->route('admin.abonnements')->with('success', 'Abonnement supprimé.');
    }
}
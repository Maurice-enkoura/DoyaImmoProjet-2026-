<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DemandeImmobiliere;
use App\Enums\StatutDemandeEnum;
use Illuminate\Http\Request;

class AdminDemandeController extends Controller
{
    public function index(Request $request)
    {
        $query = DemandeImmobiliere::with(['particulier.user', 'quartier', 'propositions']);

        // Filtre par statut
        if ($request->filled('filtre')) {
            $query->where('statut', $request->filtre);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('type_operation', 'like', "%{$search}%")
                  ->orWhere('type_bien', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('zone_recherchee', 'like', "%{$search}%")
                  ->orWhereHas('particulier.user', function ($sub) use ($search) {
                      $sub->where('nom', 'like', "%{$search}%")
                          ->orWhere('prenom', 'like', "%{$search}%")
                          ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        $demandes = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => DemandeImmobiliere::count(),
            'en_attente' => DemandeImmobiliere::where('statut', 'en_attente')->count(),
            'en_cours' => DemandeImmobiliere::where('statut', 'en_cours')->count(),
            'terminees' => DemandeImmobiliere::where('statut', 'terminee')->count(),
            'annulees' => DemandeImmobiliere::where('statut', 'annulee')->count(),
        ];

        return view('admin.demandes.index', compact('demandes', 'stats'));
    }

    public function show(DemandeImmobiliere $demande)
    {
        $demande->load([
            'particulier.user', 
            'quartier', 
            'propositions.agence.user', 
            'propositions.bien.medias'
        ]);
        return view('admin.demandes.show', compact('demande'));
    }

    public function destroy(DemandeImmobiliere $demande)
    {
        $demande->delete();
        return redirect()->route('admin.demandes.index')
            ->with('success', 'Demande supprimée avec succès.');
    }
}
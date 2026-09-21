<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\Enums\StatutPropositionEnum;
use Illuminate\Http\Request;

class AdminPropositionController extends Controller
{
    /**
     * Liste des propositions (vue admin)
     */
    public function index(Request $request)
    {
        // ✅ Relations correctes (pas de "quartier" qui n'est pas toujours une relation)
        $query = Proposition::with([
            'agence.user',
            'demande.particulier.user',
            'bien.medias',
        ]);

        // Filtre par statut
        if ($request->filled('filtre')) {
            $query->where('statut', $request->filtre);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                // Agence
                $q->whereHas('agence', function ($sub) use ($search) {
                    $sub->where('nom_agence', 'like', "%{$search}%")
                        ->orWhere('adresse', 'like', "%{$search}%");
                })
                // Demande
                ->orWhereHas('demande', function ($sub) use ($search) {
                    $sub->where('zone_recherchee', 'like', "%{$search}%");
                })
                // Bien
                ->orWhereHas('bien', function ($sub) use ($search) {
                    $sub->where('titre', 'like', "%{$search}%")
                        ->orWhere('adresse', 'like', "%{$search}%");
                })
                // Particulier
                ->orWhereHas('particulier.user', function ($sub) use ($search) {
                    $sub->where('nom', 'like', "%{$search}%")
                        ->orWhere('prenom', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $propositions = $query->orderBy('created_at', 'desc')->paginate(20);

        // ✅ Stats complètes
        $stats = [
            'total'      => Proposition::count(),
            'en_attente' => Proposition::where('statut', StatutPropositionEnum::EN_ATTENTE->value)->count(),
            'acceptees'  => Proposition::where('statut', StatutPropositionEnum::ACCEPTEE->value)->count(),
            'refusees'   => Proposition::where('statut', StatutPropositionEnum::REFUSEE->value)->count(),
            'terminees'  => Proposition::where('statut', StatutPropositionEnum::TERMINEE->value)->count(),
        ];

        return view('admin.propositions.index', compact('propositions', 'stats'));
    }

    /**
     * Détail d'une proposition (vue admin)
     */
    public function show(Proposition $proposition)
    {
        $proposition->load([
            'agence.user',
            'demande.particulier.user',
            'bien.medias',
            'medias',           // ✅ Médias propres à la proposition
            'rendezVous',       // ✅ RDV lié (pour savoir si une visite existe)
        ]);

        return view('admin.propositions.show', compact('proposition'));
    }
}
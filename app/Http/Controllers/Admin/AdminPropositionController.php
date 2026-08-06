<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Proposition;
use App\Enums\StatutPropositionEnum;
use Illuminate\Http\Request;

class AdminPropositionController extends Controller
{
    public function index(Request $request)
    {
        $query = Proposition::with(['agence.user', 'demande.particulier.user', 'bien.quartier']);

        // Filtre par statut
        if ($request->filled('filtre')) {
            $query->where('statut', $request->filtre);
        }

        // Recherche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('agence', function ($sub) use ($search) {
                    $sub->where('nom_agence', 'like', "%{$search}%");
                })->orWhereHas('demande', function ($sub) use ($search) {
                    $sub->where('type_operation', 'like', "%{$search}%")
                        ->orWhere('type_bien', 'like', "%{$search}%");
                })->orWhereHas('bien', function ($sub) use ($search) {
                    $sub->where('titre', 'like', "%{$search}%");
                });
            });
        }

        $propositions = $query->orderBy('created_at', 'desc')->paginate(20);

        $stats = [
            'total' => Proposition::count(),
            'en_attente' => Proposition::where('statut', StatutPropositionEnum::EN_ATTENTE->value)->count(),
            'acceptees' => Proposition::where('statut', StatutPropositionEnum::ACCEPTEE->value)->count(),
            'refusees' => Proposition::where('statut', StatutPropositionEnum::REFUSEE->value)->count(),
        ];

        return view('admin.propositions.index', compact('propositions', 'stats'));
    }

    public function show(Proposition $proposition)
    {
        $proposition->load([
            'agence.user', 
            'demande.particulier.user', 
            'demande.quartier',
            'bien.medias', 
            'bien.quartier'
        ]);
        return view('admin.propositions.show', compact('proposition'));
    }
}
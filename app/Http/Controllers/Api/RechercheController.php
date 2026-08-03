<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Quartier;
use App\Models\DemandeImmobiliere;
use App\Models\BienImmobilier;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SearchController extends Controller
{
    /**
     * Recherche de quartiers en autocomplétion
     */
    public function quartiers(Request $request)
    {
        $search = $request->get('q', '');
        
        $quartiers = Quartier::actif()
            ->where('nom', 'like', "%{$search}%")
            ->orWhere('ville', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'nom', 'ville']);

        return response()->json($quartiers);
    }

    /**
     * Recherche de besoins avec filtres avancés
     */
    public function besoins(Request $request)
    {
        $query = DemandeImmobiliere::with(['particulier.user', 'propositions'])
            ->where('statut', 'en_attente');

        // Recherche par mot-clé
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('zone_recherchee', 'like', "%{$search}%")
                  ->orWhere('criteres_particuliers', 'like', "%{$search}%");
            });
        }

        // Filtre par quartier
        if ($request->filled('quartier_id')) {
            $query->where('quartier_id', $request->quartier_id);
        }

        // Filtre par type de bien
        if ($request->filled('type_bien')) {
            $query->where('type_bien', $request->type_bien);
        }

        // Filtre par type d'opération
        if ($request->filled('type_operation')) {
            $query->where('type_operation', $request->type_operation);
        }

        // Filtre par budget
        if ($request->filled('budget_min')) {
            $query->where('budget_maximum', '>=', $request->budget_min);
        }
        if ($request->filled('budget_max')) {
            $query->where('budget_maximum', '<=', $request->budget_max);
        }

        // Filtre par nombre de chambres
        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', $request->chambres);
        }

        // Filtre par surface
        if ($request->filled('surface_min')) {
            $query->where('surface_minimum', '>=', $request->surface_min);
        }

        // Filtre par date d'entrée
        if ($request->filled('date_entree')) {
            $query->whereDate('date_entree_souhaitee', '<=', $request->date_entree);
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'budget_asc':
                $query->orderBy('budget_maximum', 'asc');
                break;
            case 'budget_desc':
                $query->orderBy('budget_maximum', 'desc');
                break;
            case 'propositions':
                $query->withCount('propositions')->orderBy('propositions_count', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $besoins = $query->paginate($request->get('per_page', 12));

        return response()->json($besoins);
    }

    /**
     * Recherche de biens avec filtres avancés
     */
    public function biens(Request $request)
    {
        $query = BienImmobilier::with(['agence.user', 'medias', 'quartier'])
            ->where('statut', true);

        // Recherche par mot-clé
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('adresse', 'like', "%{$search}%")
                  ->orWhere('quartier', 'like', "%{$search}%");
            });
        }

        // Filtre par quartier
        if ($request->filled('quartier_id')) {
            $query->where('quartier_id', $request->quartier_id);
        }

        // Filtre par type de bien
        if ($request->filled('type_bien')) {
            $query->where('type_bien', $request->type_bien);
        }

        // Filtre par type de contrat
        if ($request->filled('type_contrat')) {
            $query->where('type_contrat', $request->type_contrat);
        }

        // Filtre par prix
        if ($request->filled('prix_min')) {
            $query->where('prix', '>=', $request->prix_min);
        }
        if ($request->filled('prix_max')) {
            $query->where('prix', '<=', $request->prix_max);
        }

        // Filtre par surface
        if ($request->filled('surface_min')) {
            $query->where('surface', '>=', $request->surface_min);
        }
        if ($request->filled('surface_max')) {
            $query->where('surface', '<=', $request->surface_max);
        }

        // Filtre par nombre de chambres
        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', $request->chambres);
        }

        // Filtres booléens
        if ($request->has('parking')) {
            $query->where('parking_disponible', $request->boolean('parking'));
        }
        if ($request->has('meuble')) {
            $query->where('est_meuble', $request->boolean('meuble'));
        }

        // Tri
        $sort = $request->get('sort', 'recent');
        switch ($sort) {
            case 'prix_asc':
                $query->orderBy('prix', 'asc');
                break;
            case 'prix_desc':
                $query->orderBy('prix', 'desc');
                break;
            case 'surface_asc':
                $query->orderBy('surface', 'asc');
                break;
            case 'surface_desc':
                $query->orderBy('surface', 'desc');
                break;
            case 'popularite':
                $query->withCount('propositions')->orderBy('propositions_count', 'desc');
                break;
            case 'recent':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $biens = $query->paginate($request->get('per_page', 12));

        return response()->json($biens);
    }

    /**
     * Autocomplétion globale
     */
    public function autocomplete(Request $request)
    {
        $search = $request->get('q', '');
        
        if (strlen($search) < 2) {
            return response()->json([]);
        }

        $results = [];

        // Recherche de quartiers
        $quartiers = Quartier::actif()
            ->where('nom', 'like', "%{$search}%")
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'quartier',
                    'label' => $item->nom,
                    'value' => $item->id,
                    'description' => $item->ville,
                    'icon' => 'fa-solid fa-location-dot',
                ];
            });

        // Recherche de biens
        $biens = BienImmobilier::where('statut', true)
            ->where(function ($q) use ($search) {
                $q->where('titre', 'like', "%{$search}%")
                  ->orWhere('quartier', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'bien',
                    'label' => $item->titre,
                    'value' => $item->id,
                    'description' => $item->quartier . ' - ' . number_format($item->prix, 0, ',', ' ') . ' FCFA',
                    'icon' => 'fa-solid fa-building',
                    'url' => route('biens.show', $item),
                ];
            });

        // Recherche de besoins
        $besoins = DemandeImmobiliere::where('statut', 'en_attente')
            ->where(function ($q) use ($search) {
                $q->where('zone_recherchee', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'besoin',
                    'label' => $item->type_bien->label() . ' à ' . $item->zone_recherchee,
                    'value' => $item->id,
                    'description' => number_format($item->budget_maximum, 0, ',', ' ') . ' FCFA',
                    'icon' => 'fa-solid fa-home',
                    'url' => route('besoins.show', $item),
                ];
            });

        $results = array_merge($quartiers->toArray(), $biens->toArray(), $besoins->toArray());

        return response()->json($results);
    }

    /**
     * Suggestions de recherche populaires
     */
    public function suggestions(Request $request)
    {
        $suggestions = [
            'quartiers_populaires' => Quartier::withCount(['demandes', 'biens'])
                ->actif()
                ->havingRaw('(demandes_count + biens_count) > 0')
                ->orderByRaw('(demandes_count + biens_count) desc')
                ->limit(6)
                ->get()
                ->map(function ($item) {
                    return [
                        'nom' => $item->nom,
                        'count' => $item->demandes_count + $item->biens_count,
                    ];
                }),
            'types_bien' => TypeBienEnum::labels(),
            'types_contrat' => TypeContratEnum::labels(),
        ];

        return response()->json($suggestions);
    }
}
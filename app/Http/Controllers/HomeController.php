<?php

namespace App\Http\Controllers;

use App\Models\BienImmobilier;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\Quartier;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;
use App\Enums\TypeOperationEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;    
use App\Enums\StatutDemandeEnum;
class HomeController extends Controller
{
    /**
     * Page d'accueil
     */
   public function index()
    {
        // Récupérer les quartiers avec des demandes actives
        $quartiersPopulaires = Quartier::whereHas('demandes', function ($query) {
            $query->where('statut', 'en_attente');
        })
        ->withCount(['demandes' => function ($query) {
            $query->where('statut', 'en_attente');
        }])
        ->orderBy('demandes_count', 'desc')
        ->take(6)
        ->get();

        // Dernières demandes (pour le hero)
        $demandesRecentes = DemandeImmobiliere::with(['propositions', 'particulier.user'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // ✅ Derniers biens (3 derniers avec statut)
        $derniersBiens = BienImmobilier::with(['agence', 'medias', 'quartier'])
            ->where('statut', true)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get();

        // Statistiques
        $stats = [
            'besoins' => DemandeImmobiliere::where('statut', 'en_attente')->count(),
            'biens' => BienImmobilier::where('statut', true)->count(),
            'agences' => Agence::where('statut_validation', true)->count(),
        ];

        return view('home', compact(
            'quartiersPopulaires',
            'demandesRecentes',
            'derniersBiens',
            'stats'
        ));
    }

    /**
     * Page À propos
     */
    public function about()
    {
        return view('about');
    }

    /**
     * Page Contact
     */
    public function contact()
    {
        return view('contact');
    }

    /**
     * Recherche globale
     */



  public function recherche(Request $request)
    {
        $query = $request->get('q', '');
        $type = $request->get('type', 'tous');
        $typeBien = $request->get('type_bien');
        $typeContrat = $request->get('type_contrat');
        $quartierId = $request->get('quartier');

        // ==================== BIENS ====================
        $biensQuery = BienImmobilier::with(['agence.user', 'medias', 'quartier'])
            ->where('statut', true);

        if ($query) {
            $biensQuery->where(function ($q) use ($query) {
                $q->where('titre', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('adresse', 'like', "%{$query}%")
                  ->orWhereHas('agence', function ($sub) use ($query) {
                      $sub->where('nom_agence', 'like', "%{$query}%");
                  })
                  ->orWhereHas('quartier', function ($sub) use ($query) {
                      $sub->where('nom', 'like', "%{$query}%");
                  });
            });
        }

        if ($typeBien) {
            $biensQuery->where('type_bien', $typeBien);
        }

        if ($typeContrat) {
            $biensQuery->where('type_contrat', $typeContrat);
        }

        if ($quartierId) {
            $biensQuery->where('quartier_id', $quartierId);
        }

        $biens = $biensQuery->orderBy('created_at', 'desc')->paginate(12);

        // ==================== AGENCES ====================
        $agencesQuery = Agence::with(['user', 'quartier', 'evaluations'])
            ->where('statut_validation', true);

        if ($query) {
            $agencesQuery->where(function ($q) use ($query) {
                $q->where('nom_agence', 'like', "%{$query}%")
                  ->orWhere('description', 'like', "%{$query}%")
                  ->orWhere('adresse', 'like', "%{$query}%")
                  ->orWhereHas('quartier', function ($sub) use ($query) {
                      $sub->where('nom', 'like', "%{$query}%");
                  });
            });
        }

        if ($quartierId) {
            $agencesQuery->where('quartier_id', $quartierId);
        }

        $agences = $agencesQuery->orderBy('created_at', 'desc')->limit(6)->get();

        // ==================== QUARTIERS ====================
        $quartiers = Quartier::actif()->orderBy('nom')->get();

        // Types pour les filtres
        $typesBien = TypeBienEnum::labels();
        $typesContrat = TypeContratEnum::labels();

        // Compter les résultats par type
        $counts = [
            'total' => $biens->total() + $agences->count(),
            'biens' => $biens->total(),
            'agences' => $agences->count(),
        ];

        return view('recherche', compact(
            'biens',
            'agences',
            'quartiers',
            'typesBien',
            'typesContrat',
            'query',
            'type',
            'counts'
        ));
    }

    /**
     * Liste des biens avec filtres avancés
     */
   /**
 * Liste des biens immobiliers (publiques)
 */
public function biens(Request $request)
{
    $query = BienImmobilier::with(['agence.user', 'medias', 'quartier'])
        ->where('statut', true);

    // Recherche par mot-clé
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('titre', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('adresse', 'like', "%{$search}%")
              ->orWhere('quartier', 'like', "%{$search}%");
        });
    }

    // Filtre par quartier
    if ($request->filled('quartier')) {
        $query->where('quartier_id', $request->quartier);
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
        $query->where('prix', '>=', (int)$request->prix_min);
    }
    if ($request->filled('prix_max')) {
        $query->where('prix', '<=', (int)$request->prix_max);
    }

    // Filtre par surface
    if ($request->filled('surface_min')) {
        $query->where('surface', '>=', (int)$request->surface_min);
    }
    if ($request->filled('surface_max')) {
        $query->where('surface', '<=', (int)$request->surface_max);
    }

    // Filtre par nombre de chambres
    if ($request->filled('chambres')) {
        $query->where('nombre_chambres', '>=', (int)$request->chambres);
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

    $biens = $query->paginate(12);

    // Données pour les filtres
    $quartiers = Quartier::actif()->orderBy('nom')->get();
    $typesBien = collect(TypeBienEnum::cases())->mapWithKeys(function ($case) {
        return [$case->value => $case->label()];
    })->toArray();

    return view('biens.index', compact('biens', 'quartiers', 'typesBien'));
}

/**
 * Détail d'un bien
 */
public function bienShow(BienImmobilier $bien)
{
    // Incrémenter les vues
    $bien->increment('vues');
    
    $bien->load(['agence.user', 'medias', 'agence.evaluations.particulier.user', 'quartier']);

    // Biens similaires
    $biensSimilaires = BienImmobilier::with(['agence.user', 'medias'])
        ->where('statut', true)
        ->where('id', '!=', $bien->id)
        ->where('type_bien', $bien->type_bien)
        ->where('quartier', $bien->quartier)
        ->limit(4)
        ->get();

    $peutDemanderVisite = false;
    if (auth()->check() && auth()->user()->isParticulier()) {
        $peutDemanderVisite = true;
    }

    return view('biens.show', compact('bien', 'biensSimilaires', 'peutDemanderVisite'));
}

    /**
     * Liste des besoins avec filtres avancés
     */
   public function demandes(Request $request)
    {
        $query = DemandeImmobiliere::with(['particulier.user', 'propositions', 'quartier'])
            ->where('statut', StatutDemandeEnum::EN_ATTENTE); // ✅ UNIQUEMENT EN ATTENTE

        // Recherche par mot-clé
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('zone_recherchee', 'like', "%{$search}%")
                  ->orWhere('criteres_particuliers', 'like', "%{$search}%");
            });
        }

        // Filtre par quartier
        if ($request->filled('quartier')) {
            $query->where('quartier_id', $request->quartier);
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
        if ($request->filled('budget')) {
            $budget = $request->budget;
            if (strpos($budget, '-') !== false) {
                $parts = explode('-', $budget);
                if (count($parts) == 2) {
                    $query->whereBetween('budget_maximum', [(int)$parts[0], (int)$parts[1]]);
                }
            } elseif (strpos($budget, '+') !== false) {
                $min = (int)str_replace('+', '', $budget);
                $query->where('budget_maximum', '>=', $min);
            }
        }

        // Filtre par nombre de chambres
        if ($request->filled('chambres')) {
            $query->where('nombre_chambres', '>=', (int)$request->chambres);
        }

        // Filtre par surface minimum
        if ($request->filled('surface_min')) {
            $query->where('surface_minimum', '>=', (int)$request->surface_min);
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

        $demandes = $query->paginate(12);

        // Données pour les filtres
        $quartiers = Quartier::actif()->orderBy('nom')->get();
        $typesBien = collect(TypeBienEnum::cases())->mapWithKeys(function ($case) {
            return [$case->value => $case->label()];
        })->toArray();
        $typesOperation = collect(TypeOperationEnum::cases())->mapWithKeys(function ($case) {
            return [$case->value => $case->label()];
        })->toArray();

        return view('besoins.index', compact('demandes', 'quartiers', 'typesBien', 'typesOperation'));
    }

    /**
     * Détail d'une demande immobilière
     */
    public function demandeShow(DemandeImmobiliere $demande)
    {
        $demande->load([
            'particulier.user', 
            'propositions.agence.user', 
            'propositions.bien.medias',
            'quartier'
        ]);

        return view('besoins.show', compact('demande'));
    }

    /**
     * Liste des agences
     */
 public function agences(Request $request)
{
    $query = Agence::with('user')
        ->where('statut_validation', true);

    // Recherche par mot-clé
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nom_agence', 'like', "%{$search}%")
              ->orWhere('quartier', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%");
        });
    }

    // Filtre par quartier
    if ($request->filled('quartier')) {
        $query->where('quartier_id', $request->quartier);
    }

    // Tri
    $sort = $request->get('sort', 'recent');
    switch ($sort) {
        case 'note':
            $query->withAvg('evaluations', 'note')->orderBy('evaluations_avg_note', 'desc');
            break;
        case 'biens':
            $query->withCount('biens')->orderBy('biens_count', 'desc');
            break;
        case 'recent':
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    $agences = $query->paginate(12);

    $quartiers = Quartier::actif()->orderBy('nom')->get();

    // CORRECTION : Utiliser 'agences.public-index' au lieu de 'agence.public-index'
    return view('agence.public-index', compact('agences', 'quartiers'));
}

    /**
     * Détail d'une agence
     */
    public function agenceShow(Agence $agence)
    {
        $agence->load([
            'user', 
            'biens.medias', 
            'evaluations.particulier.user',
            'quartier'
        ]);

        $biens = $agence->biens()->where('statut', true)->paginate(6);

        // Statistiques de l'agence
        $stats = [
            'total_biens' => $agence->biens()->count(),
            'biens_disponibles' => $agence->biens()->where('statut', true)->count(),
            'total_evaluations' => $agence->evaluations()->count(),
            'note_moyenne' => $agence->evaluations()->avg('note') ?? 0,
            'total_propositions' => $agence->propositions()->count(),
        ];

        return view('agence.public-show', compact('agence', 'biens', 'stats'));
    }

    /**
     * Recherche AJAX pour l'autocomplétion
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
                    'url' => route('recherche', ['quartier_id' => $item->id]),
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

        // Recherche d'agences
        $agences = Agence::where('statut_validation', true)
            ->where(function ($q) use ($search) {
                $q->where('nom_agence', 'like', "%{$search}%")
                  ->orWhere('quartier', 'like', "%{$search}%");
            })
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'agence',
                    'label' => $item->nom_agence,
                    'value' => $item->id,
                    'description' => $item->quartier,
                    'icon' => 'fa-solid fa-building',
                    'url' => route('agences.public.show', $item),
                ];
            });

        $results = array_merge(
            $quartiers->toArray(),
            $biens->toArray(),
            $besoins->toArray(),
            $agences->toArray()
        );

        return response()->json($results);
    }

    /**
     * Suggestions de recherche populaires
     */
    public function suggestions()
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
            'types_bien' => collect(TypeBienEnum::cases())->mapWithKeys(function ($case) {
                return [$case->value => $case->label()];
            })->toArray(),
            'types_contrat' => collect(TypeContratEnum::cases())->mapWithKeys(function ($case) {
                return [$case->value => $case->label()];
            })->toArray(),
        ];

        return response()->json($suggestions);
    }

    /**
     * Statistiques pour la page d'accueil
     */
    public function stats()
    {
        $stats = [
            'total_demandes' => DemandeImmobiliere::where('statut', 'en_attente')->count(),
            'total_biens' => BienImmobilier::where('statut', true)->count(),
            'total_agences' => Agence::where('statut_validation', true)->count(),
            'total_rendezvous' => \App\Models\RendezVous::count(),
            'total_evaluations' => \App\Models\Evaluation::count(),
        ];

        return response()->json($stats);
    }

    /**
     * Derniers besoins publiés (pour le widget)
     */
    public function derniersBesoins()
    {
        $besoins = DemandeImmobiliere::with(['particulier.user', 'quartier'])
            ->where('statut', 'en_attente')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($besoins);
    }

    /**
     * Derniers biens publiés (pour le widget)
     */
    public function derniersBiens()
    {
        $biens = BienImmobilier::with(['agence.user', 'medias'])
            ->where('statut', true)
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json($biens);
    }

    /**
     * Recherche rapide avec filtres prédéfinis
     */
    public function rechercheRapide(Request $request)
    {
        $type = $request->get('type', 'biens');
        $filtre = $request->get('filtre');

        if ($type === 'biens') {
            $query = BienImmobilier::with(['agence.user', 'medias'])
                ->where('statut', true);

            switch ($filtre) {
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'prix_bas':
                    $query->orderBy('prix', 'asc');
                    break;
                case 'prix_haut':
                    $query->orderBy('prix', 'desc');
                    break;
                case 'surface':
                    $query->orderBy('surface', 'desc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $results = $query->limit(8)->get();
            return view('components.biens-grid', compact('results'));
        }

        if ($type === 'besoins') {
            $query = DemandeImmobiliere::with(['particulier.user'])
                ->where('statut', 'en_attente');

            switch ($filtre) {
                case 'recent':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'budget_max':
                    $query->orderBy('budget_maximum', 'desc');
                    break;
                case 'budget_min':
                    $query->orderBy('budget_maximum', 'asc');
                    break;
                default:
                    $query->orderBy('created_at', 'desc');
                    break;
            }

            $results = $query->limit(8)->get();
            return view('components.besoins-grid', compact('results'));
        }

        return response()->json([]);
    }
}
<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use App\Models\BienImmobilier;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Services\MatchingService;   
use Carbon\Carbon;
use App\Models\CreneauRendezVous;
use Illuminate\Support\Facades\DB;
use App\Models\Quartier;
class AgenceController extends Controller
{
    /**
     * Tableau de bord de l'agence
     */
   /**
 * Tableau de bord de l'agence
 */
/**
 * Tableau de bord de l'agence
 */
public function dashboard()
{
    $agence = Auth::user()->agence;

    // Abonnement
    $abonnementActuel = $agence->abonnements()
        ->where('statut', true)
        ->where('date_fin', '>', now())
        ->first();

    // Calcul des offres restantes
    $offresUtilisees = $agence->propositions()
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->count();

    $limiteOffres = $abonnementActuel ? $abonnementActuel->formule->limiteBiens() : 0;
    $offresRestantes = $abonnementActuel ? max(0, $limiteOffres - $offresUtilisees) : 0;
    $pourcentageOffres = ($abonnementActuel && $limiteOffres !== PHP_INT_MAX && $limiteOffres > 0) 
        ? round(($offresUtilisees / $limiteOffres) * 100) 
        : 0;
    $peutEnvoyerOffres = $abonnementActuel && $offresRestantes > 0;

    // Statistiques
    $stats = [
        'besoins_disponibles' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count(),
        'offres_envoyees' => $agence->propositions()->whereMonth('created_at', now()->month)->count(),
        'rendezvous_a_venir' => $agence->rendezVous()
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->count(),
        'note_moyenne' => $agence->evaluations()->avg('note') ?? 0,
    ];

    // Derniers besoins (changer le nom de la variable)
    $derniersBesoins = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)
        ->orderBy('created_at', 'desc')
        ->limit(5)
        ->get();

    // Prochains rendez-vous
    $prochainsRendezVous = $agence->rendezVous()
        ->with(['proposition.bien', 'particulier.user'])
        ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
        ->where('date_visite', '>=', now()->toDateString())
        ->orderBy('date_visite', 'asc')
        ->limit(3)
        ->get();

    // Derniers avis
    $derniersAvis = $agence->evaluations()
        ->with('particulier.user')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

    // Pour la sidebar
    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
    $rendezvousAVenir = $agence->rendezVous()
        ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
        ->where('date_visite', '>=', now()->toDateString())
        ->count();

    // Notifications et messages (pour le layout)
    $notifications = collect();
    $notificationsCount = 0;
    $messages = [];
    $messagesCount = 0;

    return view('agence.dashboard', compact(
        'stats',
        'derniersBesoins',  // Changé ici
        'prochainsRendezVous',
        'derniersAvis',
        'besoinsDisponibles',
        'rendezvousAVenir',
        'abonnementActuel',
        'offresUtilisees',
        'offresRestantes',
        'pourcentageOffres',
        'peutEnvoyerOffres',
        'notifications',
        'notificationsCount',
        'messages',
        'messagesCount'
    ));
}

    /**
     * Liste des demandes disponibles
     */
   
/**
 * Liste des demandes disponibles avec score de compatibilité
 */

protected $matchingService;

    public function __construct(MatchingService $matchingService)
    {
        $this->matchingService = $matchingService;
    }
 public function demandes(Request $request)
{
    $agence = Auth::user()->agence;
    $onglet = $request->get('onglet', 'compatibles');

    $zones = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)
        ->distinct()
        ->pluck('zone_recherchee')
        ->toArray();

    if ($onglet === 'compatibles') {
        // === DEMANDES COMPATIBLES ===
        $biens = $agence->biens()->where('statut', true)->get();
        
        if ($biens->isEmpty()) {
            $demandes = collect();
            $compteurCompatibles = 0;
            $compteurTotal = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
        } else {
            $demandesCollection = collect();
            
            // Parcourir toutes les demandes en attente
            $toutesDemandes = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->get();
            
            foreach ($toutesDemandes as $demande) {
                $meilleurScore = 0;
                $meilleurBien = null;
                $meilleursDetails = [];
                
                foreach ($biens as $bien) {
                    $match = $demande->calculerScore($bien);
                    if ($match['score'] > $meilleurScore) {
                        $meilleurScore = $match['score'];
                        $meilleurBien = $bien;
                        $meilleursDetails = $match['details'];
                    }
                }
                
                if ($meilleurScore > 0) {
                    $demandesCollection->push((object) [
                        'demande' => $demande,
                        'score' => $meilleurScore,
                        'niveau' => $this->getNiveau($meilleurScore),
                        'bien' => $meilleurBien,
                        'details' => $meilleursDetails
                    ]);
                }
            }
            
            // Trier par score décroissant
            $demandesCollection = $demandesCollection->sortByDesc('score')->values();
            $compteurCompatibles = $demandesCollection->count();
            $compteurTotal = $toutesDemandes->count();
            
            // Pagination manuelle
            $perPage = 12;
            $currentPage = $request->get('page', 1);
            $offset = ($currentPage - 1) * $perPage;
            $items = $demandesCollection->slice($offset, $perPage)->values();
            $total = $demandesCollection->count();
            
            $demandes = new \Illuminate\Pagination\LengthAwarePaginator(
                $items,
                $total,
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );
        }
        
    } else {
        // === TOUTES LES DEMANDES ===
        $query = DemandeImmobiliere::with(['particulier.user', 'propositions'])
            ->where('statut', StatutDemandeEnum::EN_ATTENTE);

        // Filtres
        if ($request->filled('zone')) {
            $query->where('zone_recherchee', $request->zone);
        }

        if ($request->filled('type_bien')) {
            $query->where('type_bien', $request->type_bien);
        }

        if ($request->filled('type_operation')) {
            $query->where('type_operation', $request->type_operation);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('zone_recherchee', 'like', "%{$search}%")
                  ->orWhere('criteres_particuliers', 'like', "%{$search}%");
            });
        }

        $demandes = $query->orderBy('created_at', 'desc')->paginate(12);
        $compteurCompatibles = $this->countDemandesCompatibles($agence);
        $compteurTotal = $demandes->total();
    }

    $stats = [
        'compatibles' => $compteurCompatibles ?? 0,
        'total' => $compteurTotal ?? $demandes->total(),
    ];

    return view('agence.demandes.index', compact('demandes', 'zones', 'onglet', 'stats'));
}

private function getNiveau(int $score): string
{
    if ($score >= 80) return 'Excellent';
    if ($score >= 60) return 'Bon';
    if ($score >= 40) return 'Moyen';
    if ($score >= 20) return 'Faible';
    return 'Minimal';
}

private function countDemandesCompatibles($agence): int
{
    $biens = $agence->biens()->where('statut', true)->get();
    if ($biens->isEmpty()) {
        return 0;
    }

    $demandes = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->get();
    $count = 0;

    foreach ($demandes as $demande) {
        foreach ($biens as $bien) {
            $match = $demande->calculerScore($bien);
            if ($match['score'] > 0) {
                $count++;
                break;
            }
        }
    }

    return $count;
}

    private function filterDemandes($demandes, Request $request)
    {
        if ($request->filled('zone')) {
            $demandes = $demandes->filter(function ($item) use ($request) {
                return $item->demande->zone_recherchee === $request->zone;
            });
        }

        if ($request->filled('type_bien')) {
            $demandes = $demandes->filter(function ($item) use ($request) {
                return $item->demande->type_bien->value === $request->type_bien;
            });
        }

        if ($request->filled('type_operation')) {
            $demandes = $demandes->filter(function ($item) use ($request) {
                return $item->demande->type_operation->value === $request->type_operation;
            });
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $demandes = $demandes->filter(function ($item) use ($search) {
                return stripos($item->demande->description, $search) !== false || 
                       stripos($item->demande->zone_recherchee, $search) !== false;
            });
        }

        return $demandes;
    }
    /**
     * Détail d'une demande
     */
    public function demandesShow(DemandeImmobiliere $demande)
    {
        $demande->load(['particulier.user']);
        return view('agence.demandes.show', compact('demande'));
    }

    /**
     * Profil de l'agence
     */
    

    /**
     * Mise à jour du profil
     */
    /**
 * Mise à jour du profil
 */
public function profil()
    {
        $agence = Auth::user()->agence;
        $documents = $agence->documents;
        
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
            
        $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
        
        $rendezvousAVenir = $agence->rendezVous()
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->count();

        // Récupérer les quartiers pour les zones d'intervention
        $quartiers = Quartier::orderBy('nom')->get();

        // Récupérer les créneaux de la semaine
        $semaine = $this->getSemaine();
        $creneaux = [];
        foreach ($semaine as $date) {
            $creneaux[$date] = CreneauRendezVous::where('agence_id', $agence->id)
                ->where('date', $date)
                ->orderBy('heure_debut')
                ->get();
        }

        // Récupérer les zones d'intervention existantes
        $zonesIntervention = $agence->zones_intervention ?? [];

        return view('agence.profil', compact(
            'agence', 
            'documents', 
            'abonnementActuel', 
            'besoinsDisponibles', 
            'rendezvousAVenir',
            'quartiers',
            'creneaux',
            'semaine',
            'zonesIntervention'
        ));
    }

    /**
     * Met à jour le profil de l'agence
     */
    public function update(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'nom_agence' => 'required|string|max:255',
            'quartier' => 'required|string|max:255',
            'adresse' => 'required|string|max:255',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'zones_intervention' => 'nullable|array',
        ]);

        $data = $request->only(['nom_agence', 'quartier', 'adresse', 'description']);

        // Gérer le logo
        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos/agences', 'public');
            $data['logo'] = $path;
        }

        // Zones d'intervention
        $data['zones_intervention'] = $request->zones_intervention ?? [];

        $agence->update($data);

        return redirect()->route('agence.profil')
            ->with('success', 'Profil mis à jour avec succès.');
    }

    // ==================== CRÉNEAUX ====================

    /**
     * Génère des créneaux horaires
     */
    public function genererCreneaux(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'jours' => 'required|array',
            'jours.*' => 'string',
            'heures' => 'required|array',
            'heures.*' => 'date_format:H:i',
        ]);

        $joursMap = [
            'Lun' => 'Monday',
            'Mar' => 'Tuesday',
            'Mer' => 'Wednesday',
            'Jeu' => 'Thursday',
            'Ven' => 'Friday',
            'Sam' => 'Saturday',
            'Dim' => 'Sunday'
        ];

        $heures = $request->heures;
        $joursSelectionnes = $request->jours;

        // Générer les dates pour les 7 prochains jours
        $dates = [];
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $jourSemaine = $date->format('l');
            $jourFr = array_search($jourSemaine, $joursMap);
            
            if (in_array($jourFr, $joursSelectionnes)) {
                $dates[] = $date->format('Y-m-d');
            }
        }

        foreach ($dates as $date) {
            foreach ($heures as $heure) {
                $heureDebut = Carbon::parse($heure);
                $heureFin = $heureDebut->copy()->addHour();

                CreneauRendezVous::updateOrCreate(
                    [
                        'agence_id' => $agence->id,
                        'date' => $date,
                        'heure_debut' => $heureDebut->format('H:i'),
                    ],
                    [
                        'heure_fin' => $heureFin->format('H:i'),
                        'est_disponible' => true,
                    ]
                );
            }
        }

        return redirect()->route('agence.profil')
            ->with('success', 'Créneaux générés avec succès.');
    }

    /**
     * Bascule la disponibilité d'un créneau
     */
    public function toggleCreneau(CreneauRendezVous $creneau)
    {
        if ($creneau->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $creneau->update(['est_disponible' => !$creneau->est_disponible]);

        return redirect()->route('agence.profil')
            ->with('success', 'Créneau mis à jour.');
    }

    /**
     * Supprime un créneau
     */
    public function supprimerCreneau(CreneauRendezVous $creneau)
    {
        if ($creneau->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $creneau->delete();

        return redirect()->route('agence.profil')
            ->with('success', 'Créneau supprimé.');
    }

    /**
     * Supprime tous les créneaux d'une date
     */
    public function supprimerCreneauxDate(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate(['date' => 'required|date']);

        CreneauRendezVous::where('agence_id', $agence->id)
            ->where('date', $request->date)
            ->delete();

        return redirect()->route('agence.profil')
            ->with('success', 'Créneaux supprimés pour cette date.');
    }

    /**
     * Récupère les créneaux disponibles (API)
     */
    public function getCreneauxDisponibles(Request $request)
    {
        try {
            $agenceId = $request->input('agence_id');
            $date = $request->input('date');

            if (!$agenceId || !$date) {
                return response()->json(['error' => 'Paramètres manquants'], 400);
            }

            $agence = Agence::find($agenceId);
            if (!$agence) {
                return response()->json(['error' => 'Agence non trouvée'], 404);
            }

            $creneaux = CreneauRendezVous::where('agence_id', $agenceId)
                ->where('date', $date)
                ->where('est_disponible', true)
                ->orderBy('heure_debut')
                ->get();

            $result = [];
            foreach ($creneaux as $creneau) {
                $result[] = [
                    'id' => $creneau->id,
                    'heure' => substr($creneau->heure_debut, 0, 5),
                    'heure_fin' => substr($creneau->heure_fin, 0, 5),
                    'label' => substr($creneau->heure_debut, 0, 5) . ' - ' . substr($creneau->heure_fin, 0, 5),
                ];
            }

            return response()->json($result);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Génère la semaine courante
     */
    private function getSemaine(): array
    {
        $semaine = [];
        for ($i = 0; $i < 7; $i++) {
            $semaine[] = Carbon::today()->addDays($i)->format('Y-m-d');
        }
        return $semaine;
    }

    /**
     * Liste des rendez-vous
     */
     public function rendezvous()
    {
        $agence = Auth::user()->agence;
        $rendezVous = RendezVous::with(['proposition.demande', 'proposition.bien', 'particulier.user'])
            ->where('agence_id', $agence->id)
            ->orderBy('date_visite', 'asc')
            ->paginate(10);
        
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
        $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
        $rendezvousAVenir = $agence->rendezVous()
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->count();

        return view('agence.rendezvous.index', compact('rendezVous', 'abonnementActuel', 'besoinsDisponibles', 'rendezvousAVenir'));
    }


    public function rendezvousShow(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $rendezVous->load(['proposition.bien', 'proposition.demande', 'particulier.user']);

        return view('agence.rendezvous.show', compact('rendezVous'));
    }

    /**
     * Mise à jour d'un rendez-vous
     */
   public function rendezvousUpdate(Request $request, RendezVous $rendezVous)
    {
        $request->validate([
            'statut' => 'required|in:confirme,annule,termine',
        ]);

        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $rendezVous->update(['statut' => $request->statut]);

        $message = 'Statut du rendez-vous mis à jour.';
        
        // Si le rendez-vous est confirmé, les numéros deviennent visibles
        if ($request->statut === 'confirme') {
            $message .= ' Les coordonnées téléphoniques sont maintenant visibles.';
        }

        return redirect()->route('agence.rendezvous.index')
            ->with('success', $message);
    }

    /**
     * Liste des évaluations
     */
  public function evaluations()
{
    $agence = Auth::user()->agence;
    
    // Charger les relations sans 'proposition' si elle n'existe pas
    $evaluations = Evaluation::with(['particulier.user'])
        ->where('agence_id', $agence->id)
        ->orderBy('created_at', 'desc')
        ->paginate(10);
    
    $abonnementActuel = $agence->abonnements()
        ->where('statut', true)
        ->where('date_fin', '>', now())
        ->first();
    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
    $rendezvousAVenir = $agence->rendezVous()
        ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
        ->where('date_visite', '>=', now()->toDateString())
        ->count();

    return view('agence.evaluations.index', compact('evaluations', 'abonnementActuel', 'besoinsDisponibles', 'rendezvousAVenir'));
}

    /**
     * Affiche le détail d'un avis
     */
    public function evaluationsShow(Evaluation $evaluation)
    {
        if ($evaluation->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $evaluation->load(['particulier.user', 'proposition.bien']);

        $agence = Auth::user()->agence;
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
        $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
        $rendezvousAVenir = $agence->rendezVous()
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->count();

        return view('agence.evaluations.show', compact('evaluation', 'abonnementActuel', 'besoinsDisponibles', 'rendezvousAVenir'));
    }

    /**
     * Répondre à un avis
     */
   /**
 * Répondre à un avis
 */
public function evaluationsRepondre(Request $request, Evaluation $evaluation)
{
    if ($evaluation->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    $request->validate([
        'reponse' => 'required|string|min:5|max:2000', // ✅ Changé de min:10 à min:5
    ]);

    $evaluation->update([
        'reponse_agence' => $request->reponse,
        'date_reponse' => now(),
    ]);

    return redirect()->route('agence.evaluations.show', $evaluation)
        ->with('success', 'Votre réponse a été publiée avec succès.');
}


    /**
     * Gestion de l'abonnement
     */
   /**
 * Gestion de l'abonnement
 */
public function abonnement()
{
    $agence = Auth::user()->agence;
    
    $abonnementActuel = $agence->abonnements()
        ->where('statut', true)
        ->where('date_fin', '>', now())
        ->first();

    // Vérifier si l'abonnement gratuit est expiré
    $abonnementGratuitExpire = false;
    $dernierAbonnement = $agence->abonnements()
        ->where('formule', 'basic')
        ->where('statut', false)
        ->orderBy('date_fin', 'desc')
        ->first();

    if ($dernierAbonnement && $dernierAbonnement->date_fin < now()) {
        $abonnementGratuitExpire = true;
    }

    $historique = $agence->abonnements()
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();

    // Plans disponibles
    $plans = [
        'basic' => [
            'label' => 'Basique',
            'price' => 0,
            'price_label' => 'Gratuit',
            'features' => [
                '5 offres envoyées / mois',
                'Accès aux besoins publics',
                'Profil agence'
            ],
            'limite' => 5,
            'badge' => null
        ],
        'standard' => [
            'label' => 'Standard',
            'price' => 10000,
            'price_label' => '10 000 F',
            'features' => [
                '30 offres envoyées / mois',
                'Mise en avant des annonces',
                'Profil agence',
            ],
            'limite' => 30,
            'badge' => null
        ],
        'premium' => [
            'label' => 'Premium',
            'price' => 15000,
            'price_label' => '15 000 F',
            'features' => [
                'Offres illimitées',
                'Badge "Agence Premium"',
                'Accès anticipé aux nouveaux besoins',
                'Profil agence',
            ],
            'limite' => PHP_INT_MAX,
            'badge' => 'Recommandé'
        ]
    ];

    return view('agence.abonnement.index', compact(
        'abonnementActuel',
        'historique',
        'plans',
        'abonnementGratuitExpire'
    ));
}

    /**
     * Historique des activités
     */
   /**
 * Historique des activités de l'agence
 */
public function historique()
{
    $agence = Auth::user()->agence;
    
    $activites = collect();
    
    // Propositions
    $propositions = $agence->propositions()
        ->with('demande')
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function ($item) {
            return [
                'type' => 'proposition',
                'titre' => 'Offre envoyée',
                'description' => $item->demande->type_bien->label() . ' — ' . number_format($item->prix_propose, 0, ',', ' ') . ' FCFA',
                'date' => $item->created_at,
                'statut' => $item->statut->label(),
                'statut_class' => $this->getStatusClass($item->statut->value),
            ];
        });
    
    // Rendez-vous
    $rendezVous = $agence->rendezVous()
        ->with(['proposition.bien', 'particulier.user'])
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function ($item) {
            return [
                'type' => 'rendezvous',
                'titre' => 'Rendez-vous',
                'description' => ($item->proposition->bien->titre ?? 'Bien') . ' — ' . ($item->particulier->user->prenom ?? 'Client'),
                'date' => $item->created_at,
                'statut' => $item->statut->label(),
                'statut_class' => $this->getStatusClass($item->statut->value),
            ];
        });
    
    // Évaluations
    $evaluations = $agence->evaluations()
        ->with('particulier.user')
        ->orderBy('created_at', 'desc')
        ->limit(20)
        ->get()
        ->map(function ($item) {
            return [
                'type' => 'evaluation',
                'titre' => 'Avis reçu',
                'description' => ($item->particulier->user->prenom ?? 'Client') . ' ' . ($item->particulier->user->nom ?? '') . ' — ' . $item->note . '/5',
                'date' => $item->created_at,
                'statut' => $item->note . '★',
                'statut_class' => $item->note >= 4 ? 'success' : 'default',
            ];
        });
    
    // Fusionner et trier
    $activites = $propositions->concat($rendezVous)->concat($evaluations)
        ->sortByDesc('date')
        ->values();
    
    // Pagination manuelle
    $perPage = 15;
    $currentPage = request()->get('page', 1);
    $offset = ($currentPage - 1) * $perPage;
    $items = $activites->slice($offset, $perPage)->values();
    $total = $activites->count();
    
    $activites = new \Illuminate\Pagination\LengthAwarePaginator(
        $items,
        $total,
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );
    
    $abonnementActuel = $agence->abonnements()
        ->where('statut', true)
        ->where('date_fin', '>', now())
        ->first();
    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
    $rendezvousAVenir = $agence->rendezVous()
        ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
        ->where('date_visite', '>=', now()->toDateString())
        ->count();

    return view('agence.historique', compact('activites', 'abonnementActuel', 'besoinsDisponibles', 'rendezvousAVenir'));
}

    private function getStatusClass($status)
    {
        $map = [
            'en_attente' => 'warning',
            'acceptee' => 'success',
            'refusee' => 'danger',
            'terminee' => 'success',
            'annulee' => 'danger',
            'planifie' => 'warning',
            'confirme' => 'info',
            'annule' => 'danger',
            'termine' => 'success',
        ];
        
        return $map[$status] ?? 'default';
    }
}
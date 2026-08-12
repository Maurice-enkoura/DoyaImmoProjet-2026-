<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use App\Models\BienImmobilier;
use App\Models\Abonnement;
use App\Models\Quartier;
use App\Models\CreneauRendezVous;
use App\Enums\FormuleAbonnementEnum;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Services\MatchingService;
use App\Services\PayDunyaService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// ===== NOTIFICATIONS =====
use App\Notifications\NouvelleDemandeCompatibleNotification;
use App\Notifications\RendezVousDemandeNotification;
use App\Notifications\RendezVousAnnuleNotification;
use App\Notifications\RendezVousConfirmeNotification;
use App\Notifications\RappelVisiteNotification;
use App\Notifications\NouvelleEvaluationNotification;
use App\Notifications\AbonnementExpireNotification;
use App\Notifications\AgenceValideeNotification;

class AgenceController extends Controller
{
    /**
     * Matching Service
     */
    protected $matchingService;

    /**
     * PayDunya Service
     */
    protected $paydunya;

    public function __construct(MatchingService $matchingService, PayDunyaService $paydunya)
    {
        $this->matchingService = $matchingService;
        $this->paydunya = $paydunya;
    }

    // ==================== NOTIFICATIONS ====================

    /**
     * Récupère les notifications de l'utilisateur
     */
    private function getNotifications()
    {
        $user = Auth::user();
        
        if ($user && method_exists($user, 'unreadNotifications')) {
            return [
                'notifications' => $user->unreadNotifications()->limit(10)->get(),
                'notificationsCount' => $user->unreadNotifications()->count(),
            ];
        }
        
        return [
            'notifications' => collect(),
            'notificationsCount' => 0,
        ];
    }

    // ==================== DASHBOARD ====================

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

        // Derniers besoins
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

        // Notifications
        $notifData = $this->getNotifications();

        $messages = [];
        $messagesCount = 0;

        return view('agence.dashboard', array_merge(compact(
            'stats',
            'derniersBesoins',
            'prochainsRendezVous',
            'derniersAvis',
            'besoinsDisponibles',
            'rendezvousAVenir',
            'abonnementActuel',
            'offresUtilisees',
            'offresRestantes',
            'pourcentageOffres',
            'peutEnvoyerOffres',
            'messages',
            'messagesCount'
        ), $notifData));
    }

    // ==================== GESTION DES BIENS EN VEDETTE ====================

    /**
     * Affiche la liste des biens de l'agence avec option vedette
     */
    public function biensVedette(Request $request)
    {
        $agence = Auth::user()->agence;

        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        $peutMettreEnVedette = $abonnementActuel && 
            in_array($abonnementActuel->formule->value, ['premium', 'pro']);

        $biens = $agence->biens()
            ->with(['medias', 'quartier'])
            ->orderBy('est_vedette', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        $stats = [
            'total' => $agence->biens()->count(),
            'disponibles' => $agence->biens()->where('statut', true)->count(),
            'en_vedette' => $agence->biens()->where('est_vedette', true)->count(),
            'max_vedette' => $this->getMaxVedette($abonnementActuel),
        ];

        $notifData = $this->getNotifications();

        return view('agence.biens.vedette', array_merge(compact(
            'biens',
            'stats',
            'abonnementActuel',
            'peutMettreEnVedette'
        ), $notifData));
    }

    /**
     * Active la mise en vedette d'un bien
     */
    public function activerVedette(Request $request, BienImmobilier $bien)
    {
        try {
            $agence = Auth::user()->agence;

            if ($bien->agence_id !== $agence->id) {
                return redirect()->back()
                    ->with('error', 'Ce bien ne vous appartient pas.');
            }

            $abonnementActuel = $agence->abonnements()
                ->where('statut', true)
                ->where('date_fin', '>', now())
                ->first();

            if (!$abonnementActuel || !in_array($abonnementActuel->formule->value, ['premium', 'pro'])) {
                return redirect()->back()
                    ->with('error', 'Vous devez avoir un abonnement Premium ou Pro pour mettre un bien en vedette.');
            }

            $maxVedette = $this->getMaxVedette($abonnementActuel);
            $vedettesActuelles = $agence->biens()->where('est_vedette', true)->count();

            if ($vedettesActuelles >= $maxVedette) {
                return redirect()->back()
                    ->with('error', 'Vous avez atteint le nombre maximum de biens en vedette (' . $maxVedette . ').');
            }

            $bien->update([
                'est_vedette' => true,
                'vedette_fin' => now()->addDays(30),
            ]);

            return redirect()->back()
                ->with('success', 'Le bien "' . $bien->titre . '" est maintenant en vedette pour 30 jours !');

        } catch (\Exception $e) {
            Log::error('Erreur activation vedette: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de l\'activation de la vedette.');
        }
    }

    /**
     * Désactive la mise en vedette d'un bien
     */
    public function desactiverVedette(BienImmobilier $bien)
    {
        try {
            $agence = Auth::user()->agence;

            if ($bien->agence_id !== $agence->id) {
                return redirect()->back()
                    ->with('error', 'Ce bien ne vous appartient pas.');
            }

            $bien->update([
                'est_vedette' => false,
                'vedette_fin' => null,
            ]);

            return redirect()->back()
                ->with('success', 'Le bien "' . $bien->titre . '" n\'est plus en vedette.');

        } catch (\Exception $e) {
            Log::error('Erreur désactivation vedette: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erreur lors de la désactivation de la vedette.');
        }
    }

    /**
     * Retourne le nombre maximum de vedettes selon l'abonnement
     */
    private function getMaxVedette($abonnement)
    {
        if (!$abonnement) return 0;
        
        return match($abonnement->formule->value) {
            'basic' => 0,
            'premium' => 3,
            'pro' => PHP_INT_MAX,
            default => 0,
        };
    }

    /**
     * Vérifie et désactive automatiquement les vedettes expirées
     */
    public function verifierVedettesExpirees()
    {
        $biensExpires = BienImmobilier::where('est_vedette', true)
            ->where('vedette_fin', '<', now())
            ->get();

        foreach ($biensExpires as $bien) {
            $bien->update([
                'est_vedette' => false,
                'vedette_fin' => null,
            ]);
        }

        return $biensExpires->count();
    }

    // ==================== DEMANDES ====================

    /**
     * Liste des demandes disponibles avec score de compatibilité
     */
    public function demandes(Request $request)
    {
        $agence = Auth::user()->agence;
        $onglet = $request->get('onglet', 'compatibles');

        $zones = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)
            ->distinct()
            ->pluck('zone_recherchee')
            ->toArray();

        if ($onglet === 'compatibles') {
            $biens = $agence->biens()->where('statut', true)->get();
            
            if ($biens->isEmpty()) {
                $perPage = 12;
                $currentPage = $request->get('page', 1);
                
                $demandes = new \Illuminate\Pagination\LengthAwarePaginator(
                    collect(),
                    0,
                    $perPage,
                    $currentPage,
                    ['path' => $request->url(), 'query' => $request->query()]
                );
                
                $compteurCompatibles = 0;
                $compteurTotal = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
            } else {
                $demandesCollection = collect();
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
                
                $demandesCollection = $demandesCollection->sortByDesc('score')->values();
                $compteurCompatibles = $demandesCollection->count();
                $compteurTotal = $toutesDemandes->count();
                
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
            $query = DemandeImmobiliere::with(['particulier.user', 'propositions'])
                ->where('statut', StatutDemandeEnum::EN_ATTENTE);

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

        $notifData = $this->getNotifications();

        return view('agence.demandes.index', array_merge(compact(
            'demandes', 
            'zones', 
            'onglet', 
            'stats'
        ), $notifData));
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

    /**
     * Détail d'une demande
     */
    public function demandesShow(DemandeImmobiliere $demande)
    {
        $demande->load(['particulier.user']);
        $notifData = $this->getNotifications();
        return view('agence.demandes.show', array_merge(compact('demande'), $notifData));
    }

    // ==================== PROFIL ====================

    /**
     * Profil de l'agence
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

        $quartiers = Quartier::orderBy('nom')->get();

        $semaine = $this->getSemaine();
        $creneaux = [];
        foreach ($semaine as $date) {
            $creneaux[$date] = CreneauRendezVous::where('agence_id', $agence->id)
                ->where('date', $date)
                ->orderBy('heure_debut')
                ->get();
        }

        $zonesIntervention = $agence->zones_intervention ?? [];

        $notifData = $this->getNotifications();

        return view('agence.profil', array_merge(compact(
            'agence', 
            'documents', 
            'abonnementActuel', 
            'besoinsDisponibles', 
            'rendezvousAVenir',
            'quartiers',
            'creneaux',
            'semaine',
            'zonesIntervention'
        ), $notifData));
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

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos/agences', 'public');
            $data['logo'] = $path;
        }

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
     * Gestion des créneaux horaires - Page d'index
     */
    public function creneauxIndex()
    {
        $agence = Auth::user()->agence;
        
        $semaine = $this->getSemaine();
        $creneaux = [];
        foreach ($semaine as $date) {
            $creneaux[$date] = CreneauRendezVous::where('agence_id', $agence->id)
                ->where('date', $date)
                ->orderBy('heure_debut')
                ->get();
        }

        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
        
        $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();
        $rendezvousAVenir = $agence->rendezVous()
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->count();

        $notifData = $this->getNotifications();

        return view('agence.creneaux.index', array_merge(compact(
            'semaine',
            'creneaux',
            'abonnementActuel',
            'besoinsDisponibles',
            'rendezvousAVenir'
        ), $notifData));
    }

    // ==================== RENDEZ-VOUS ====================

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

        $notifData = $this->getNotifications();

        return view('agence.rendezvous.index', array_merge(compact(
            'rendezVous', 
            'abonnementActuel', 
            'besoinsDisponibles', 
            'rendezvousAVenir'
        ), $notifData));
    }

    /**
     * Détail d'un rendez-vous
     */
    public function rendezvousShow(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $rendezVous->load(['proposition.bien', 'proposition.demande', 'particulier.user']);

        $notifData = $this->getNotifications();

        return view('agence.rendezvous.show', array_merge(compact('rendezVous'), $notifData));
    }

    /**
     * Mise à jour d'un rendez-vous - AVEC NOTIFICATIONS
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

        if ($request->statut === 'confirme') {
            $message .= ' Les coordonnées téléphoniques sont maintenant visibles.';
            $rendezVous->particulier->user->notify(new RendezVousConfirmeNotification($rendezVous));
        }

        if ($request->statut === 'annule') {
            $message .= ' Le rendez-vous a été annulé.';
            $rendezVous->particulier->user->notify(new RendezVousAnnuleNotification($rendezVous));
        }

        return redirect()->route('agence.rendezvous.index')
            ->with('success', $message);
    }

    /**
     * Confirmer un rendez-vous
     */
    public function rendezvousConfirmer(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $rendezVous->update(['statut' => StatutRendezVousEnum::CONFIRME]);

        try {
            $rendezVous->particulier->user->notify(new RendezVousConfirmeNotification($rendezVous));
        } catch (\Exception $e) {
            Log::error('Erreur notification confirmation: ' . $e->getMessage());
        }

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous confirmé avec succès.');
    }

    /**
     * Annuler un rendez-vous
     */
    public function rendezvousAnnuler(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        if ($rendezVous->creneau_id) {
            $creneau = CreneauRendezVous::find($rendezVous->creneau_id);
            if ($creneau) {
                $creneau->update(['est_disponible' => true]);
            }
        }

        $rendezVous->update(['statut' => StatutRendezVousEnum::ANNULE]);

        try {
            $rendezVous->particulier->user->notify(new RendezVousAnnuleNotification($rendezVous));
        } catch (\Exception $e) {
            Log::error('Erreur notification annulation: ' . $e->getMessage());
        }

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous annulé avec succès.');
    }

    /**
     * Marquer un rendez-vous comme terminé
     */
    public function rendezvousTermine(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        try {
            $rendezVous->update(['statut' => StatutRendezVousEnum::TERMINE]);

            $proposition = $rendezVous->proposition;
            if ($proposition) {
                $proposition->update(['statut' => StatutPropositionEnum::TERMINEE]);
                
                $demande = $proposition->demande;
                if ($demande) {
                    $demande->update(['statut' => StatutDemandeEnum::TERMINEE]);
                }
            }

            return redirect()->route('agence.rendezvous.index')
                ->with('success', 'Rendez-vous terminé avec succès ! La demande est maintenant clôturée.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de la clôture du rendez-vous: ' . $e->getMessage());
            return redirect()->route('agence.rendezvous.index')
                ->with('error', 'Une erreur est survenue lors de la clôture du rendez-vous.');
        }
    }

    // ==================== ÉVALUATIONS ====================

    /**
     * Liste des évaluations
     */
    public function evaluations()
    {
        $agence = Auth::user()->agence;
        
        $evaluations = Evaluation::with(['particulier.user', 'proposition.bien'])
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

        $notifData = $this->getNotifications();

        return view('agence.evaluations.index', array_merge(compact(
            'evaluations', 
            'abonnementActuel', 
            'besoinsDisponibles', 
            'rendezvousAVenir'
        ), $notifData));
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

        $notifData = $this->getNotifications();

        return view('agence.evaluations.show', array_merge(compact(
            'evaluation', 
            'abonnementActuel', 
            'besoinsDisponibles', 
            'rendezvousAVenir'
        ), $notifData));
    }

    /**
     * Répondre à un avis
     */
    public function evaluationsRepondre(Request $request, Evaluation $evaluation)
    {
        if ($evaluation->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $request->validate([
            'reponse' => 'required|string|min:5|max:2000',
        ]);

        $evaluation->update([
            'reponse_agence' => $request->reponse,
            'date_reponse' => now(),
        ]);

        return redirect()->route('agence.evaluations.show', $evaluation)
            ->with('success', 'Votre réponse a été publiée avec succès.');
    }

    // ==================== ABONNEMENT ====================

    /**
     * Gestion de l'abonnement
     */
   /**
 * Gestion de l'abonnement
 */
/**
 * Gestion de l'abonnement
 */
public function abonnement()
{
    $agence = Auth::user()->agence;
    
    if (!$agence) {
        return redirect()->route('agence.dashboard')
            ->with('error', 'Agence non trouvée.');
    }

    // ✅ Déclarer la variable avec une valeur par défaut
    $estNonValidee = false;

    // ✅ Vérifier si l'agence est validée
    if (!$agence->statut_validation) {
        $estNonValidee = true;
        
        $notifData = $this->getNotifications();
        
        return view('agence.abonnement.index', array_merge([
            'agence' => $agence,
            'abonnementActuel' => null,
            'historique' => collect(),
            'plans' => [],
            'abonnementGratuitExpire' => false,
            'estNonValidee' => $estNonValidee,
        ], $notifData))->with('error', 'Votre agence doit être validée par un administrateur pour accéder à cette fonctionnalité.');
    }

    $abonnementActuel = $agence->abonnements()
        ->where('statut', true)
        ->where('date_fin', '>', now())
        ->first();

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

    $plans = [
        'basic' => [
            'label' => 'Basique',
            'price' => 0,
            'price_label' => 'Gratuit',
            'period' => '1 mois',
            'features' => [
                '5 offres envoyées / mois',
                'Accès aux besoins publics',
                'Profil agence'
            ],
            'limite' => 5,
            'badge' => null,
            'color' => '#6A7280',
            'icon' => 'fa-regular fa-star'
        ],
        'premium' => [
            'label' => 'Premium',
            'price' => 200,
            'price_label' => '200 FCFA',
            'period' => '1 mois',
            'features' => [
                '20 offres envoyées / mois',
                'Mise en avant des annonces',
                'Badge "Agence Premium"',
                'Accès anticipé aux nouveaux besoins',
                'Profil agence optimisé',
                '3 biens en vedette'
            ],
            'limite' => 20,
            'badge' => 'Populaire',
            'color' => '#B5502A',
            'icon' => 'fa-solid fa-crown'
        ],
        'pro' => [
            'label' => 'Pro',
            'price' => 500,
            'price_label' => '500 FCFA',
            'period' => '1 mois',
            'features' => [
                'Offres illimitées',
                'Badge "Agence Pro"',
                'Mise en avant prioritaire',
                'Accès anticipé exclusif',
                'Profil agence complet',
                'Support prioritaire',
                'Biens en vedette illimités'
            ],
            'limite' => PHP_INT_MAX,
            'badge' => 'Recommandé',
            'color' => '#D4AF37',
            'icon' => 'fa-solid fa-gem'
        ]
    ];

    $notifData = $this->getNotifications();

    // ✅ Maintenant la variable est définie dans tous les cas
    return view('agence.abonnement.index', array_merge(compact(
        'abonnementActuel',
        'historique',
        'plans',
        'abonnementGratuitExpire',
        'agence',
        'estNonValidee'
    ), $notifData));
}

    /**
     * Souscrire à un abonnement
     */
    /**
 * Souscrire à un abonnement
 */
public function souscrire(Request $request)
{
    try {
        $request->validate([
            'formule' => 'required|in:basic,premium,pro'
        ]);

        $agence = Auth::user()->agence;
        
        if (!$agence) {
            return redirect()->route('agence.dashboard')
                ->with('error', 'Agence non trouvée.');
        }

        // ✅ Vérifier si l'agence est validée
        if (!$agence->statut_validation) {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Votre agence doit être validée par un administrateur pour souscrire à un abonnement.');
        }

        $formule = \App\Enums\FormuleAbonnementEnum::from($request->formule);
        $montant = $formule->prix();

        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        if ($abonnementActuel && $abonnementActuel->formule->value === 'basic' && $montant > 0) {
            $abonnementActuel->update(['statut' => false]);
        } elseif ($abonnementActuel && $montant > 0) {
            return redirect()->route('agence.abonnement')
                ->with('info', 'Vous avez déjà un abonnement actif. Vous pouvez le mettre à jour.');
        } elseif ($abonnementActuel && $montant == 0) {
            return redirect()->route('agence.abonnement')
                ->with('info', 'Vous avez déjà un abonnement gratuit actif.');
        }

        if ($montant == 0) {
            $aDejaEuGratuit = $agence->abonnements()
                ->where('formule', 'basic')
                ->where('statut', false)
                ->exists();

            if ($aDejaEuGratuit) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Vous avez déjà utilisé votre abonnement gratuit. Veuillez choisir un abonnement payant.');
            }
        }

        $abonnement = \App\Models\Abonnement::create([
            'agence_id' => $agence->id,
            'formule' => $formule,
            'montant' => $montant,
            'date_debut' => now(),
            'date_fin' => now()->addMonth(),
            'statut' => $montant == 0,
        ]);

        if ($montant > 0) {
            return redirect()->route('paydunya.pay', ['abonnement' => $abonnement->id]);
        }

        return redirect()->route('agence.abonnement')
            ->with('success', 'Abonnement gratuit activé avec succès !');

    } catch (\Exception $e) {
        Log::error('Erreur souscription: ' . $e->getMessage());
        return redirect()->route('agence.abonnement')
            ->with('error', 'Erreur lors de la souscription: ' . $e->getMessage());
    }
}

    /**
     * Mettre à jour/Changer d'abonnement
     */
    public function upgrade(Request $request)
    {
        try {
            $request->validate([
                'formule' => 'required|in:basic,premium,pro'
            ]);

            $agence = Auth::user()->agence;
            
            if (!$agence) {
                return redirect()->route('agence.dashboard')
                    ->with('error', 'Agence non trouvée.');
            }

            $formule = \App\Enums\FormuleAbonnementEnum::from($request->formule);
            $montant = $formule->prix();

            Abonnement::where('agence_id', $agence->id)
                ->where('statut', true)
                ->update(['statut' => false]);

            $abonnement = \App\Models\Abonnement::create([
                'agence_id' => $agence->id,
                'formule' => $formule,
                'montant' => $montant,
                'date_debut' => now(),
                'date_fin' => now()->addMonths(12),
                'statut' => $montant == 0,
            ]);

            if ($montant > 0) {
                return redirect()->route('paydunya.pay', ['abonnement' => $abonnement->id]);
            }

            return redirect()->route('agence.abonnement')
                ->with('success', 'Abonnement mis à jour avec succès !');

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour abonnement: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    /**
     * Annuler un abonnement
     */
    public function annuler(Abonnement $abonnement)
    {
        try {
            $agence = Auth::user()->agence;
            
            if ($abonnement->agence_id != $agence->id) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Action non autorisée.');
            }

            $abonnement->update([
                'statut' => false,
                'paydunya_status' => 'cancelled',
            ]);

            return redirect()->route('agence.abonnement')
                ->with('success', 'Abonnement annulé avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur annulation: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de l\'annulation.');
        }
    }

    // ==================== HISTORIQUE ====================

    /**
     * Historique des activités de l'agence
     */
    public function historique()
    {
        $agence = Auth::user()->agence;
        
        $activites = collect();
        
        $propositions = $agence->propositions()
            ->with('demande')
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'proposition',
                    'titre' => 'Offre envoyée',
                    'description' => ($item->demande->type_bien->label() ?? 'Bien') . ' — ' . number_format($item->prix_propose, 0, ',', ' ') . ' FCFA',
                    'date' => $item->created_at,
                    'statut' => $item->statut->label(),
                    'statut_class' => $this->getStatusClass($item->statut->value),
                ];
            });
        
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
        
        $activites = $propositions->concat($rendezVous)->concat($evaluations)
            ->sortByDesc('date')
            ->values();
        
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

        $notifData = $this->getNotifications();

        return view('agence.historique', array_merge(compact(
            'activites', 
            'abonnementActuel', 
            'besoinsDisponibles', 
            'rendezvousAVenir'
        ), $notifData));
    }

    // ==================== UTILITAIRES ====================

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
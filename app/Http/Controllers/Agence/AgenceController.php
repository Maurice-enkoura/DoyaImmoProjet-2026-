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
use App\Models\MiseEnVedette;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\TypeBienEnum;
use App\Enums\TypeOperationEnum;
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

        // ✅ Calcul des offres restantes avec gestion d'erreur
        $offresUtilisees = $agence->propositions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // ✅ Sécuriser la récupération de la limite d'offres
        $limiteOffres = 0;
        if ($abonnementActuel) {
            try {
                // Vérifier si la formule existe et est valide
                if ($abonnementActuel->formule) {
                    $limiteOffres = $abonnementActuel->formule->limiteOffres();
                } else {
                    $limiteOffres = 5; // Valeur par défaut pour Basic
                }
            } catch (\Exception $e) {
                // Si l'ENUM est invalide, on traite comme Basic
                $limiteOffres = 5;
                \Log::warning('Formule d\'abonnement invalide pour l\'agence ID: ' . $agence->id);
            }
        }

        $offresRestantes = $abonnementActuel ? max(0, $limiteOffres - $offresUtilisees) : 0;
        $pourcentageOffres = ($abonnementActuel && $limiteOffres !== PHP_INT_MAX && $limiteOffres > 0)
            ? round(($offresUtilisees / $limiteOffres) * 100)
            : 0;
        $peutEnvoyerOffres = $abonnementActuel && $offresRestantes > 0;

        // ✅ Récupérer les IDs des besoins actifs (en_attente ou en_cours)
        $besoinsActifsIds = DemandeImmobiliere::whereIn('statut', [
            StatutDemandeEnum::EN_ATTENTE->value,
            StatutDemandeEnum::EN_COURS->value
        ])->pluck('id')->toArray();

        // Statistiques
        $stats = [
            // ✅ Besoins disponibles : UNIQUEMENT les demandes en attente
            'besoins_disponibles' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)->count(),

            // ✅ Offres envoyées : UNIQUEMENT sur besoins actifs ET ce mois-ci
            'offres_envoyees' => $agence->propositions()
                ->whereIn('demande_id', $besoinsActifsIds)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),

            // ✅ Rendez-vous à venir : planifié ou confirmé
            'rendezvous_a_venir' => $agence->rendezVous()
                ->whereIn('statut', [
                    StatutRendezVousEnum::PLANIFIE->value,
                    StatutRendezVousEnum::CONFIRME->value
                ])
                ->where('date_visite', '>=', now()->toDateString())
                ->count(),

            // ✅ Note moyenne
            'note_moyenne' => $agence->evaluations()->avg('note') ?? 0,
        ];

        // Derniers besoins disponibles
        $derniersBesoins = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($besoin) {
                if (is_string($besoin->statut)) {
                    $besoin->statut = StatutDemandeEnum::from($besoin->statut);
                }
                if (is_string($besoin->type_bien)) {
                    $besoin->type_bien = TypeBienEnum::from($besoin->type_bien);
                }
                if (is_string($besoin->type_operation)) {
                    $besoin->type_operation = TypeOperationEnum::from($besoin->type_operation);
                }
                return $besoin;
            });

        // Prochains rendez-vous
        $prochainsRendezVous = $agence->rendezVous()
            ->with(['proposition.bien', 'particulier.user'])
            ->whereIn('statut', [
                StatutRendezVousEnum::PLANIFIE->value,
                StatutRendezVousEnum::CONFIRME->value
            ])
            ->where('date_visite', '>=', now()->toDateString())
            ->orderBy('date_visite', 'asc')
            ->limit(3)
            ->get()
            ->map(function ($rdv) {
                if (is_string($rdv->statut)) {
                    $rdv->statut = StatutRendezVousEnum::from($rdv->statut);
                }
                return $rdv;
            });

        // Derniers avis
        $derniersAvis = $agence->evaluations()
            ->with('particulier.user')
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // Pour la sidebar
        $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)->count();
        $rendezvousAVenir = $agence->rendezVous()
            ->whereIn('statut', [
                StatutRendezVousEnum::PLANIFIE->value,
                StatutRendezVousEnum::CONFIRME->value
            ])
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
            'limiteOffres',
            'pourcentageOffres',
            'peutEnvoyerOffres',
            'messages',
            'messagesCount'
        ), $notifData));
    }
    
    // ==================== GESTION DES BIENS EN VEDETTE ====================

    /**
     * Désactive la mise en vedette d'un bien - UTILISE LE SLUG
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
                'vedette_debut' => null,
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
                'vedette_debut' => null,
                'vedette_fin' => null,
            ]);
        }

        return $biensExpires->count();
    }

    /**
     * Affiche la page de demande de mise en vedette - UTILISE LE SLUG
     */
    public function demandeVedette(BienImmobilier $bien)
    {
        $agence = Auth::user()->agence;

        if ($bien->agence_id !== $agence->id) {
            abort(403, 'Ce bien ne vous appartient pas.');
        }

        // ✅ Vérifier l'abonnement Pro
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        if (!$abonnementActuel || $abonnementActuel->formule->value !== 'pro') {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Seules les agences avec un abonnement Pro peuvent demander une mise en vedette.');
        }

        if ($bien->est_vedette) {
            return redirect()->route('agence.biens.show', ['bien' => $bien->slug])
                ->with('warning', 'Ce bien est déjà en vedette.');
        }

        // ✅ Tarifs de mise en vedette
        $tarifs = [
            1 => 1000,
            3 => 1500,
            7 => 3000,
            14 => 5000,
            30 => 8000,
        ];

        return view('agence.biens.demande-vedette', compact('bien', 'tarifs'));
    }

    /**
     * Afficher les coordonnées pour contacter DoyaImmo
     */
    public function contactVedette(MiseEnVedette $mise)
    {
        $agence = Auth::user()->agence;

        if ($mise->agence_id !== $agence->id) {
            abort(403);
        }

        if ($mise->statut !== 'en_attente') {
            return redirect()->route('agence.biens.show', ['bien' => $mise->bien->slug])
                ->with('info', 'Cette demande a déjà été traitée.');
        }

        $bien = $mise->bien;
        $duree = $mise->duree;
        $montant = $mise->montant;

        return view('agence.biens.vedette-contact', compact('mise', 'bien', 'duree', 'montant'));
    }

    /**
     * Vérifier si l'agence peut publier des biens (abonnement Pro)
     */
    private function peutPublierBiens($agence): bool
    {
        $abonnement = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        return $abonnement && $abonnement->formule->value === 'pro';
    }

    /**
     * Enregistrer une demande de mise en vedette - UTILISE LE SLUG
     */
    public function demanderVedette(Request $request, BienImmobilier $bien)
    {
        $agence = Auth::user()->agence;

        if ($bien->agence_id !== $agence->id) {
            abort(403);
        }

        $request->validate([
            'duree' => 'required|in:1,3,7,14,30',
        ]);

        // Vérifier l'abonnement Pro
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        if (!$abonnementActuel || $abonnementActuel->formule->value !== 'pro') {
            return redirect()->route('agence.abonnement')
                ->with('error', 'Seules les agences avec un abonnement Pro peuvent demander une mise en vedette.');
        }

        if ($bien->est_vedette) {
            return redirect()->route('agence.biens.show', ['bien' => $bien->slug])
                ->with('warning', 'Ce bien est déjà en vedette.');
        }

        $duree = (int) $request->duree;
        $tarifs = [
            1 => 1000,
            3 => 1500,
            7 => 3000,
            14 => 5000,
            30 => 8000,
        ];

        if (!isset($tarifs[$duree])) {
            return redirect()->back()->with('error', 'Durée invalide.');
        }

        // ✅ CRÉER LA DEMANDE EN BASE DE DONNÉES
        $mise = MiseEnVedette::create([
            'bien_id' => $bien->id,
            'agence_id' => $agence->id,
            'duree' => $duree,
            'montant' => $tarifs[$duree],
            'statut' => 'en_attente',
            'date_debut' => null,
            'date_fin' => null,
        ]);

        // ✅ NOTIFIER LES ADMINISTRATEURS
        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new \App\Notifications\NouvelleDemandeVedetteNotification($mise));
            } catch (\Exception $e) {
                \Log::error('Erreur notification admin pour la demande #' . $mise->id . ': ' . $e->getMessage());
            }
        }

        // Rediriger vers la page de contact avec l'ID de la demande
        return redirect()->route('agence.biens.vedette.contact', ['mise' => $mise->id])
            ->with('success', 'Votre demande de mise en vedette a été enregistrée. Contactez-nous pour finaliser le paiement.');
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
     * Détail d'une demande - UTILISE LE SLUG
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
    
    // ==================== GESTION DES CRÉNEAUX RÉCURRENTS ====================

    /**
     * Sauvegarder le planning type (créneaux récurrents) dans la session
     */
    public function sauvegarderPlanning(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'plannings' => 'required|array',
            'plannings.*.jour' => 'required|integer|min:0|max:6',
            'plannings.*.heure_debut' => 'required|date_format:H:i',
            'plannings.*.heure_fin' => 'required|date_format:H:i|after:plannings.*.heure_debut',
        ]);

        // Sauvegarder dans la session ou base de données (vous pouvez créer une table planning_types)
        session()->put('planning_type_' . $agence->id, $request->plannings);

        // Générer les créneaux pour la semaine en cours
        $this->genererCreneauxDepuisPlanning($agence);

        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', 'Planning type sauvegardé avec succès ! Les créneaux ont été générés pour la semaine.');
    }

    /**
     * Générer les créneaux pour la semaine à partir du planning type
     */
    private function genererCreneauxDepuisPlanning($agence)
    {
        // Récupérer le planning type de la session
        $planningType = session()->get('planning_type_' . $agence->id, []);

        if (empty($planningType)) {
            return 0;
        }

        $creneauxCrees = 0;
        $joursMap = [
            0 => 'Lun',
            1 => 'Mar',
            2 => 'Mer',
            3 => 'Jeu',
            4 => 'Ven',
            5 => 'Sam',
            6 => 'Dim'
        ];

        // Générer pour les 7 prochains jours
        for ($i = 0; $i < 7; $i++) {
            $date = Carbon::today()->addDays($i);
            $jourSemaine = $date->dayOfWeek; // 0=Lundi, 6=Dimanche (Carbon)

            // Ajuster pour que 0=Lundi (Carbon: 0=Dimanche, 1=Lundi, ...)
            $jourSemaineCarbon = $jourSemaine === 0 ? 6 : $jourSemaine - 1;

            // Récupérer les plannings pour ce jour
            $planningsJour = array_filter($planningType, function ($p) use ($jourSemaineCarbon) {
                return $p['jour'] == $jourSemaineCarbon;
            });

            foreach ($planningsJour as $planning) {
                // Vérifier si le créneau existe déjà
                $existe = CreneauRendezVous::where('agence_id', $agence->id)
                    ->where('date', $date->format('Y-m-d'))
                    ->where('heure_debut', $planning['heure_debut'])
                    ->exists();

                if (!$existe) {
                    CreneauRendezVous::create([
                        'agence_id' => $agence->id,
                        'date' => $date->format('Y-m-d'),
                        'heure_debut' => $planning['heure_debut'],
                        'heure_fin' => $planning['heure_fin'],
                        'est_disponible' => true,
                    ]);
                    $creneauxCrees++;
                }
            }
        }

        return $creneauxCrees;
    }

    /**
     * Générer automatiquement les créneaux pour la semaine
     */
    public function genererCreneauxAuto(Request $request)
    {
        $agence = Auth::user()->agence;

        $creneauxCrees = $this->genererCreneauxDepuisPlanning($agence);

        if ($creneauxCrees === 0) {
            return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
                ->with('warning', 'Aucun créneau généré. Vérifiez que vous avez un planning type configuré.');
        }

        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', "{$creneauxCrees} créneaux générés automatiquement pour la semaine.");
    }

    /**
     * Modifier la méthode genererCreneaux existante pour intégrer la récurrence
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
            'Lun' => 0,
            'Mar' => 1,
            'Mer' => 2,
            'Jeu' => 3,
            'Ven' => 4,
            'Sam' => 5,
            'Dim' => 6
        ];

        $heures = $request->heures;
        $joursSelectionnes = $request->jours;
        $estRecurrent = $request->boolean('est_recurrent', false);

        if ($estRecurrent) {
            // Créer le planning type
            $planningType = [];
            foreach ($joursSelectionnes as $jourFr) {
                $jourNum = $joursMap[$jourFr] ?? 0;
                foreach ($heures as $heure) {
                    $heureDebut = Carbon::parse($heure);
                    $heureFin = $heureDebut->copy()->addHour();

                    $planningType[] = [
                        'jour' => $jourNum,
                        'heure_debut' => $heureDebut->format('H:i'),
                        'heure_fin' => $heureFin->format('H:i'),
                    ];
                }
            }

            // Sauvegarder le planning type
            session()->put('planning_type_' . $agence->id, $planningType);

            // Générer les créneaux pour la semaine
            $this->genererCreneauxDepuisPlanning($agence);

            return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
                ->with('success', 'Planning type sauvegardé et créneaux générés pour la semaine.');
        } else {
            // Génération ponctuelle (comportement existant)
            $dates = [];
            for ($i = 0; $i < 7; $i++) {
                $date = Carbon::today()->addDays($i);
                $jourSemaine = $date->dayOfWeek;
                $jourSemaineCarbon = $jourSemaine === 0 ? 6 : $jourSemaine - 1;
                $jourFr = array_search($jourSemaineCarbon, $joursMap);

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

            return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
                ->with('success', 'Créneaux générés avec succès.');
        }
    }

    /**
     * Afficher le planning type actuel
     */
    public function getPlanningType()
    {
        $agence = Auth::user()->agence;
        $planningType = session()->get('planning_type_' . $agence->id, []);

        $joursFr = ['Lun', 'Mar', 'Mer', 'Jeu', 'Ven', 'Sam', 'Dim'];
        $result = [];

        foreach ($planningType as $planning) {
            $jour = $planning['jour'];
            if (!isset($result[$jour])) {
                $result[$jour] = [
                    'jour' => $jour,
                    'jour_fr' => $joursFr[$jour] ?? '?',
                    'creneaux' => []
                ];
            }
            $result[$jour]['creneaux'][] = [
                'heure_debut' => $planning['heure_debut'],
                'heure_fin' => $planning['heure_fin']
            ];
        }

        return response()->json(array_values($result));
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
    
    // ==================== CRÉNEAUX ====================

    /**
     * Supprime un créneau spécifique
     */
    public function supprimerCreneau(CreneauRendezVous $creneau)
    {
        if ($creneau->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $creneau->delete();

        // Rediriger vers le profil avec l'onglet "creneaux" actif
        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', 'Créneau supprimé avec succès.');
    }

    /**
     * Supprime tous les créneaux d'une date
     */
    public function supprimerCreneauxDate(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate(['date' => 'required|date']);

        $deleted = CreneauRendezVous::where('agence_id', $agence->id)
            ->where('date', $request->date)
            ->delete();

        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', $deleted . ' créneau(x) supprimé(s) pour cette date.');
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
            // ✅ Mettre à jour le statut du rendez-vous
            $rendezVous->update([
                'statut' => StatutRendezVousEnum::TERMINE->value
            ]);

            // ✅ Mettre à jour la proposition
            $proposition = $rendezVous->proposition;
            if ($proposition) {
                $proposition->update([
                    'statut' => StatutPropositionEnum::TERMINEE->value
                ]);

                // ✅ Mettre à jour la demande (besoin)
                $demande = $proposition->demande;
                if ($demande && $demande->statut === StatutDemandeEnum::EN_COURS->value) {
                    $demande->update([
                        'statut' => StatutDemandeEnum::TERMINEE->value
                    ]);
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
    public function abonnement()
    {
        $agence = Auth::user()->agence;

        if (!$agence) {
            return redirect()->route('agence.dashboard')
                ->with('error', 'Agence non trouvée.');
        }

        $estNonValidee = false;

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
                'aDejaEuGratuit' => false, // ✅ Ajouté
                'isBasicActif' => false, // ✅ Ajouté
                'isProActif' => false, // ✅ Ajouté
            ], $notifData));
        }

        // ✅ Vérifier l'abonnement actuel
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        // ✅ Vérifier si l'agence a déjà eu un abonnement gratuit (Basic) terminé
        $aDejaEuGratuit = $agence->abonnements()
            ->where('formule', 'basic')
            ->where('statut', false)
            ->exists();

        // ✅ Vérifier si Basic est actif
        $isBasicActif = false;
        $isProActif = false;

        if ($abonnementActuel) {
            if ($abonnementActuel->formule->value === 'basic') {
                $isBasicActif = true;
            } elseif ($abonnementActuel->formule->value === 'pro') {
                $isProActif = true;
            }
        }

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

        // ✅ Utilisation de l'Enum pour générer les plans
        $plans = [];
        $formules = ['basic', 'pro'];

        foreach ($formules as $key) {
            $formule = FormuleAbonnementEnum::from($key);

            $plans[$key] = [
                'label' => $formule->label(),
                'price' => $formule->prix(),
                'price_label' => $formule->prixMensuel(),
                'period' => '1 mois',
                'features' => $formule->fonctionnalites(),
                'limite' => $formule->limiteOffres(),
                'badge' => $formule->badge(),
                'color' => $formule->couleur(),
                'icon' => $formule->icone(),
            ];
        }

        // ✅ Tarifs de mise en vedette
        $tarifsVedette = [
            1 => 1000,
            3 => 1500,
            7 => 3000,
            14 => 5000,
            30 => 8000,
        ];

        $notifData = $this->getNotifications();

        return view('agence.abonnement.index', array_merge(compact(
            'abonnementActuel',
            'historique',
            'plans',
            'abonnementGratuitExpire',
            'agence',
            'estNonValidee',
            'tarifsVedette',
            'aDejaEuGratuit', // ✅ Ajouté
            'isBasicActif', // ✅ Ajouté
            'isProActif' // ✅ Ajouté
        ), $notifData));
    }

    /**
     * Souscrire à un abonnement
     */
    public function souscrire(Request $request)
    {
        try {
            $request->validate([
                'formule' => 'required|in:basic,pro'
            ]);

            $agence = Auth::user()->agence;

            if (!$agence) {
                return redirect()->route('agence.dashboard')
                    ->with('error', 'Agence non trouvée.');
            }

            if (!$agence->statut_validation) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Votre agence doit être validée par un administrateur pour souscrire à un abonnement.');
            }

            $formule = FormuleAbonnementEnum::from($request->formule);
            $montant = $formule->prix();

            // ✅ Vérifier si l'agence a déjà un abonnement actif
            $abonnementActuel = $agence->abonnements()
                ->where('statut', true)
                ->where('date_fin', '>', now())
                ->first();

            if ($abonnementActuel) {
                $formuleActuelle = $abonnementActuel->formule->value;
                $formuleDemandee = $request->formule;

                // ✅ Si l'utilisateur a déjà un abonnement Basic actif
                if ($formuleActuelle === $formuleDemandee) {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà un abonnement ' . $formule->label() . ' actif jusqu\'au ' . $abonnementActuel->date_fin->format('d/m/Y') . '.');
                }

                // ✅ Basic → peut passer à Pro (on désactive l'ancien)
                if ($formuleActuelle === 'basic' && $formuleDemandee === 'pro') {
                    $abonnementActuel->update(['statut' => false]);
                }
                // ✅ Pro → bloquer tout changement
                elseif ($formuleActuelle === 'pro') {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà un abonnement Pro actif jusqu\'au ' . $abonnementActuel->date_fin->format('d/m/Y') . '.');
                } else {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous ne pouvez pas changer d\'abonnement pour le moment.');
                }
            }

            // ✅ Vérifier si l'agence a déjà eu un abonnement gratuit (Basic) terminé
            if ($montant == 0) {
                $aDejaEuGratuit = $agence->abonnements()
                    ->where('formule', 'basic')
                    ->where('statut', false)
                    ->exists();

                if ($aDejaEuGratuit) {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà utilisé votre abonnement gratuit. Veuillez choisir l\'abonnement Pro.');
                }
            }

            // ✅ Créer l'abonnement
            $abonnement = Abonnement::create([
                'agence_id' => $agence->id,
                'formule' => $formule,
                'montant' => $montant,
                'date_debut' => now(),
                'date_fin' => now()->addMonth(),
                'statut' => $montant == 0, // Si gratuit, activé immédiatement
            ]);

            // ✅ Si payant, rediriger vers PayDunya
            if ($montant > 0) {
                return redirect()->route('paydunya.pay', ['abonnement' => $abonnement->id]);
            }

            // ✅ Si gratuit, activer et rediriger
            return redirect()->route('agence.abonnement')
                ->with('success', '🎉 Abonnement gratuit activé avec succès !');
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
     * Affiche uniquement les activités terminées ou annulées
     */
    public function historique()
    {
        $agence = Auth::user()->agence;

        $activites = collect();

        // ✅ Propositions (offres envoyées) - UNIQUEMENT terminées ou annulées
        $propositions = $agence->propositions()
            ->with(['demande', 'bien.medias'])
            ->whereIn('statut', [
                StatutPropositionEnum::TERMINEE->value,
                StatutPropositionEnum::REFUSEE->value
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'proposition',
                    'titre' => 'Offre envoyée',
                    'description' => ($item->demande->type_bien->label() ?? 'Bien') . ' — ' . number_format($item->prix_propose, 0, ',', ' ') . ' FCFA',
                    'date' => $item->created_at,
                    'statut' => $item->statut->label(),
                    'statut_class' => $this->getStatusClass($item->statut->value),
                    'medias' => $item->bien ? $item->bien->medias : collect(),
                    'details' => [
                        'Bien' => $item->bien->titre ?? 'N/A',
                        'Surface' => ($item->bien->surface ?? 0) . ' m²',
                        'Prix proposé' => number_format($item->prix_propose, 0, ',', ' ') . ' FCFA',
                        'Demande ID' => '#' . $item->demande_id,
                        'Type de demande' => $item->demande->type_bien->label() ?? 'N/A',
                        'Zone recherchée' => $item->demande->zone_recherchee ?? 'N/A',
                        'Budget client' => number_format($item->demande->budget_maximum, 0, ',', ' ') . ' FCFA',
                    ],
                    'link' => route('agence.propositions.show', $item),
                    'demande_link' => route('agence.demandes.show', ['demande' => $item->demande->slug]), // ✅ SLUG
                ];
            });

        // ✅ Rendez-vous - UNIQUEMENT terminés ou annulés
        $rendezVous = $agence->rendezVous()
            ->with(['proposition.bien.medias', 'particulier.user', 'proposition.demande'])
            ->whereIn('statut', [
                StatutRendezVousEnum::TERMINE->value,
                StatutRendezVousEnum::ANNULE->value
            ])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'rendezvous',
                    'titre' => 'Rendez-vous',
                    'description' => ($item->proposition->bien->titre ?? 'Bien') . ' — ' . ($item->particulier->user->prenom ?? 'Client'),
                    'date' => $item->created_at,
                    'statut' => $item->statut->label(),
                    'statut_class' => $this->getStatusClass($item->statut->value),
                    'medias' => $item->proposition->bien ? $item->proposition->bien->medias : collect(),
                    'details' => [
                        'Bien' => $item->proposition->bien->titre ?? 'N/A',
                        'Date visite' => $item->date_visite->format('d/m/Y'),
                        'Heure' => $item->heure_visite,
                        'Client' => $item->particulier->user->prenom ?? 'N/A',
                    ],
                    'link' => route('agence.rendezvous.show', $item),
                ];
            });

        // ✅ Évaluations (avis reçus) - UNIQUEMENT terminées (toutes les évaluations sont considérées comme terminées)
        $evaluations = $agence->evaluations()
            ->with(['particulier.user', 'proposition.bien.medias', 'proposition.demande'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'type' => 'evaluation',
                    'titre' => 'Avis reçu',
                    'description' => ($item->particulier->user->prenom ?? 'Client') . ' ' . ($item->particulier->user->nom ?? '') . ' — ' . $item->note . '/5',
                    'date' => $item->created_at,
                    'statut' => $item->note . '★',
                    'statut_class' => $item->note >= 4 ? 'success' : 'default',
                    'medias' => $item->proposition && $item->proposition->bien ? $item->proposition->bien->medias : collect(),
                    'details' => [
                        'Note' => $item->note . '/5',
                        'Client' => $item->particulier->user->prenom ?? 'N/A',
                        'Commentaire' => $item->commentaire ?? 'Aucun commentaire',
                    ],
                ];
            });

        // Fusionner toutes les activités
        $activites = $propositions->concat($rendezVous)->concat($evaluations)
            ->sortByDesc('date')
            ->values();

        // Pagination
        $perPage = request()->get('per_page', 15);
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

    /**
     * Retourne la classe CSS selon le statut
     */
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

    // ==================== UTILITAIRES ====================
}
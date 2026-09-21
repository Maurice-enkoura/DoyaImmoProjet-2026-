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
use Illuminate\Support\Facades\Notification;

// ===== NOTIFICATIONS =====
use App\Notifications\NouvelleDemandeCompatibleNotification;
use App\Notifications\RendezVousDemandeNotification;
use App\Notifications\RendezVousAnnuleNotification;
use App\Notifications\RendezVousConfirmeNotification;
use App\Notifications\RendezVousEffectueNotification;
use App\Notifications\RendezVousClientAbsentNotification;
use App\Notifications\RappelVisiteNotification;
use App\Notifications\NouvelleEvaluationNotification;
use App\Notifications\AbonnementExpireNotification;
use App\Notifications\AgenceValideeNotification;

class AgenceController extends Controller
{
    protected $matchingService;
    protected $paydunya;

    public function __construct(MatchingService $matchingService, PayDunyaService $paydunya)
    {
        $this->matchingService = $matchingService;
        $this->paydunya = $paydunya;
    }

    // ==================== NOTIFICATIONS ====================

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

   public function dashboard()
{
    $agence = Auth::user()->agence;

    $abonnementActif = config('abonnement.actif', false);
    $modeGratuit = config('abonnement.mode_gratuit', true);

    if ($abonnementActif && !$modeGratuit) {
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        $offresUtilisees = $agence->propositions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $limiteOffres = $abonnementActuel ? $abonnementActuel->formule->limiteOffres() : 0;
        $offresRestantes = $abonnementActuel ? max(0, $limiteOffres - $offresUtilisees) : 0;
        $pourcentageOffres = ($abonnementActuel && $limiteOffres !== PHP_INT_MAX && $limiteOffres > 0)
            ? round(($offresUtilisees / $limiteOffres) * 100)
            : 0;
        $peutEnvoyerOffres = $abonnementActuel && $offresRestantes > 0;
        $estPro = $abonnementActuel && $abonnementActuel->formule->value === 'pro';
    } else {
        $abonnementActuel = (object) [
            'formule' => (object) [
                'value' => 'pro',
                'label' => 'Pro (gratuit)',
                'limiteOffres' => function () { return PHP_INT_MAX; },
                'prixMensuel' => function () { return '0 FCFA (offert)'; },
            ],
            'date_fin' => now()->addYears(10),
            'statut' => true,
        ];

        $offresUtilisees = $agence->propositions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $limiteOffres = PHP_INT_MAX;
        $offresRestantes = PHP_INT_MAX;
        $pourcentageOffres = 0;
        $peutEnvoyerOffres = true;
        $estPro = true;
    }

    $stats = [
        'besoins_disponibles' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)->count(),
        'offres_envoyees' => $agence->propositions()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count(),
        'rendezvous_a_venir' => $agence->rendezVous()
            ->whereIn('statut', [
                StatutRendezVousEnum::PLANIFIE->value,
                StatutRendezVousEnum::CONFIRME->value
            ])
            ->where('date_visite', '>=', now()->toDateString())
            ->count(),
        'note_moyenne' => $agence->evaluations()->avg('note') ?? 0,
    ];

    $derniersBesoins = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)
        ->orderBy('created_at', 'desc')
        ->limit(6)
        ->get()
        ->map(function ($besoin) {
            if (is_string($besoin->statut)) {
                $besoin->statut = StatutDemandeEnum::from($besoin->statut);
            }
            if (is_string($besoin->type_bien)) {
                $besoin->type_bien = TypeBienEnum::from($besoin->type_bien);
            }
            return $besoin;
        });

    $prochainsRendezVous = $agence->rendezVous()
        ->with(['proposition.bien', 'particulier.user'])
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value
        ])
        ->where('date_visite', '>=', now()->toDateString())
        ->orderBy('date_visite', 'asc')
        ->limit(3)
        ->get();

    // ✅ Uniquement les avis NON RÉPONDUS (boîte de réception)
    $derniersAvis = $agence->evaluations()
        ->with('particulier.user')
        ->whereNull('reponse_agence')
        ->orderBy('created_at', 'desc')
        ->limit(3)
        ->get();

    // ✅ Compteur réel d'avis à traiter (indépendant de la limite de 3)
    $nbAvisATraiter = $agence->evaluations()
        ->whereNull('reponse_agence')
        ->count();

    $biensEnVedette = $agence->biens()
        ->where('est_vedette', true)
        ->where('vedette_fin', '>', now())
        ->with('medias')
        ->latest()
        ->take(6)
        ->get();

    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)->count();

    $rendezvousAVenir = $agence->rendezVous()
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value
        ])
        ->where('date_visite', '>=', now()->toDateString())
        ->count();

    $notifData = $this->getNotifications();

    $messages = [];
    $messagesCount = 0;

    return view('agence.dashboard', array_merge(compact(
        'stats',
        'derniersBesoins',
        'prochainsRendezVous',
        'derniersAvis',
        'nbAvisATraiter',        // ← AJOUTÉ
        'besoinsDisponibles',
        'rendezvousAVenir',
        'abonnementActuel',
        'offresUtilisees',
        'offresRestantes',
        'limiteOffres',
        'pourcentageOffres',
        'peutEnvoyerOffres',
        'messages',
        'messagesCount',
        'abonnementActif',
        'modeGratuit',
        'estPro',
        'biensEnVedette'
    ), $notifData));
}

    // ==================== GESTION DES BIENS EN VEDETTE ====================

    public function desactiverVedette(BienImmobilier $bien)
    {
        try {
            $agence = Auth::user()->agence;

            if ($bien->agence_id !== $agence->id) {
                return redirect()->back()->with('error', 'Ce bien ne vous appartient pas.');
            }

            $bien->update([
                'est_vedette' => false,
                'vedette_debut' => null,
                'vedette_fin' => null,
            ]);

            return redirect()->back()->with('success', 'Le bien "' . $bien->titre . '" n\'est plus en vedette.');
        } catch (\Exception $e) {
            Log::error('Erreur désactivation vedette: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Erreur lors de la désactivation de la vedette.');
        }
    }

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

    public function demandeVedette(BienImmobilier $bien)
    {
        $agence = Auth::user()->agence;

        if ($bien->agence_id !== $agence->id) {
            abort(403);
        }

        if (config('abonnement.actif', false)) {
            $abonnementActuel = $agence->abonnements()
                ->where('statut', true)
                ->where('date_fin', '>', now())
                ->first();

            if (!$abonnementActuel || $abonnementActuel->formule->value !== 'pro') {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Seules les agences avec un abonnement Pro peuvent demander une mise en vedette.');
            }
        }

        $tarifs = [
            1  => 1000,
            3  => 1500,
            7  => 3000,
            14 => 5000,
            30 => 8000,
        ];

        return view('agence.biens.demande-vedette', compact('bien', 'tarifs'));
    }

    public function demanderVedette(Request $request, BienImmobilier $bien)
    {
        $agence = Auth::user()->agence;

        if ($bien->agence_id !== $agence->id) {
            abort(403);
        }

        $request->validate([
            'duree' => 'required|in:1,3,7,14,30',
        ]);

        if (config('abonnement.actif', false)) {
            $abonnementActuel = $agence->abonnements()
                ->where('statut', true)
                ->where('date_fin', '>', now())
                ->first();

            if (!$abonnementActuel || $abonnementActuel->formule->value !== 'pro') {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Seules les agences avec un abonnement Pro peuvent demander une mise en vedette.');
            }
        }

        if ($bien->est_vedette) {
            return redirect()->route('agence.biens.show', ['bien' => $bien->slug])
                ->with('warning', 'Ce bien est déjà en vedette.');
        }

        $duree = (int) $request->duree;
        $tarifs = [
            1  => 1000,
            3  => 1500,
            7  => 3000,
            14 => 5000,
            30 => 8000,
        ];

        if (!isset($tarifs[$duree])) {
            return redirect()->back()->with('error', 'Durée invalide.');
        }

        $mise = MiseEnVedette::create([
            'bien_id'    => $bien->id,
            'agence_id'  => $agence->id,
            'duree'      => $duree,
            'montant'    => $tarifs[$duree],
            'statut'     => 'en_attente',
            'date_debut' => null,
            'date_fin'   => null,
        ]);

        $admins = \App\Models\User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            try {
                $admin->notify(new \App\Notifications\NouvelleDemandeVedetteNotification($mise));
            } catch (\Exception $e) {
                Log::error('Erreur notification admin pour la demande #' . $mise->id . ': ' . $e->getMessage());
            }
        }

        return redirect()->route('agence.biens.vedette.contact', ['mise' => $mise->id])
            ->with('success', 'Votre demande de mise en vedette a été enregistrée. Contactez-nous pour finaliser le paiement.');
    }

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

    private function peutPublierBiens($agence): bool
    {
        $abonnement = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        return $abonnement && $abonnement->formule->value === 'pro';
    }

    // ==================== DEMANDES ====================

    public function demandes(Request $request)
    {
        DemandeImmobiliere::supprimerDemandesExpirees();

        $agence = Auth::user()->agence;
        $onglet = $request->get('onglet', 'compatibles');

        $filters = [
            'zone' => $request->get('zone'),
            'type_bien' => $request->get('type_bien'),
            'type_operation' => $request->get('type_operation'),
            'search' => $request->get('search'),
        ];

        $zones = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)
            ->distinct()
            ->pluck('zone_recherchee')
            ->toArray();

        $baseQuery = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE);

        if ($filters['zone']) {
            $baseQuery->where('zone_recherchee', $filters['zone']);
        }
        if ($filters['type_bien']) {
            $baseQuery->where('type_bien', $filters['type_bien']);
        }
        if ($filters['type_operation']) {
            $baseQuery->where('type_operation', $filters['type_operation']);
        }
        if ($filters['search']) {
            $baseQuery->where(function ($q) use ($filters) {
                $q->where('zone_recherchee', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('description', 'like', '%' . $filters['search'] . '%');
            });
        }

        $toutesDemandesFiltrees = $baseQuery->get();
        $compteurTotal = $toutesDemandesFiltrees->count();

        if ($onglet === 'compatibles') {
            $biens = $agence->biens()->get();

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
            } else {
                $demandesCollection = collect();

                foreach ($toutesDemandesFiltrees as $demande) {
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
            $query = $baseQuery->with(['particulier.user', 'propositions']);

            $demandes = $query->orderBy('created_at', 'desc')->paginate(12);

            $biens = $agence->biens()->get();
            $demandes->getCollection()->transform(function ($demande) use ($biens) {
                $meilleurScore = 0;
                $meilleurBien = null;

                foreach ($biens as $bien) {
                    $match = $demande->calculerScore($bien);
                    if ($match['score'] > $meilleurScore) {
                        $meilleurScore = $match['score'];
                        $meilleurBien = $bien;
                    }
                }

                $demande->score = $meilleurScore;
                $demande->niveau = $meilleurScore > 0 ? $this->getNiveau($meilleurScore) : 'Aucune correspondance';
                $demande->bien = $meilleurBien;

                return $demande;
            });

            $compatibles = $demandes->getCollection()->filter(fn($d) => $d->score > 0);
            $nonCompatibles = $demandes->getCollection()->filter(fn($d) => $d->score == 0);

            $compteurCompatibles = $toutesDemandesFiltrees->filter(function ($demande) use ($biens) {
                foreach ($biens as $bien) {
                    $match = $demande->calculerScore($bien);
                    if ($match['score'] > 0) {
                        return true;
                    }
                }
                return false;
            })->count();

            $demandesCollection = $compatibles->concat($nonCompatibles);

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

        $stats = [
            'compatibles' => $compteurCompatibles ?? 0,
            'total' => $compteurTotal,
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
        if ($score >= 80) return 'Excellent ';
        if ($score >= 60) return 'Bon';
        if ($score >= 40) return 'Moyen ';
        if ($score >= 20) return 'Faible';
        return 'Minimal';
    }

    private function countDemandesCompatibles($agence): int
    {
        $biens = $agence->biens()->get();

        if ($biens->isEmpty()) {
            return 0;
        }

        $demandes = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)
            ->where('created_at', '>', now()->subDays(30))
            ->get();

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

    public function demandesShow(DemandeImmobiliere $demande)
    {
        $demande->load(['particulier.user']);
        $notifData = $this->getNotifications();
        return view('agence.demandes.show', array_merge(compact('demande'), $notifData));
    }

    // ==================== PROFIL ====================

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

        $creneauxParJour = [];
        foreach ($semaine as $date) {
            $creneauxParJour[$date] = CreneauRendezVous::where('agence_id', $agence->id)
                ->where('date', $date)
                ->orderBy('heure_debut')
                ->get();
        }

        $creneaux = [
            'semaine' => $semaine,
            'creneaux' => $creneauxParJour,
        ];

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
            'zonesIntervention'
        ), $notifData));
    }

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

        return redirect()->route('agence.profil')->with('success', 'Profil mis à jour avec succès.');
    }

    // ==================== GESTION DES CRÉNEAUX RÉCURRENTS ====================

    public function sauvegarderPlanning(Request $request)
    {
        $agence = Auth::user()->agence;

        $request->validate([
            'plannings' => 'required|array',
            'plannings.*.jour' => 'required|integer|min:0|max:6',
            'plannings.*.heure_debut' => 'required|date_format:H:i',
            'plannings.*.heure_fin' => 'required|date_format:H:i|after:plannings.*.heure_debut',
        ]);

        \App\Models\PlanningType::where('agence_id', $agence->id)->delete();

        foreach ($request->plannings as $planning) {
            \App\Models\PlanningType::create([
                'agence_id' => $agence->id,
                'jour' => $planning['jour'],
                'heure_debut' => $planning['heure_debut'],
                'heure_fin' => $planning['heure_fin'],
            ]);
        }

        $creneauxCrees = $this->genererCreneauxDepuisPlanning($agence, 30);

        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', 'Planning type sauvegardé avec succès ! ' . ($creneauxCrees > 0 ? "{$creneauxCrees} nouveaux créneaux générés pour les 30 prochains jours." : 'Les créneaux sont déjà à jour.'));
    }

    public function genererCreneauxDepuisPlanning($agence, int $nbJours = 30): int
    {
        $planningsBdd = \App\Models\PlanningType::where('agence_id', $agence->id)->get();

        $planningType = [];
        if ($planningsBdd->isNotEmpty()) {
            foreach ($planningsBdd as $p) {
                $planningType[] = [
                    'jour' => (int) $p->jour,
                    'heure_debut' => substr($p->heure_debut, 0, 5),
                    'heure_fin' => substr($p->heure_fin, 0, 5),
                ];
            }
        } else {
            $planningType = session()->get('planning_type_' . $agence->id, []);
        }

        if (empty($planningType)) {
            return 0;
        }

        $creneauxCrees = 0;

        for ($i = 0; $i < $nbJours; $i++) {
            $date = Carbon::today()->addDays($i);
            $jourSemaine = $date->dayOfWeek;
            $jourSemaineCarbon = $jourSemaine === 0 ? 6 : $jourSemaine - 1;

            $planningsJour = array_filter($planningType, function ($p) use ($jourSemaineCarbon) {
                return (int) $p['jour'] === (int) $jourSemaineCarbon;
            });

            foreach ($planningsJour as $planning) {
                $heureDebut = strlen($planning['heure_debut']) === 5 ? $planning['heure_debut'] : substr($planning['heure_debut'], 0, 5);
                $heureFin = strlen($planning['heure_fin']) === 5 ? $planning['heure_fin'] : substr($planning['heure_fin'], 0, 5);

                $existe = CreneauRendezVous::where('agence_id', $agence->id)
                    ->where('date', $date->format('Y-m-d'))
                    ->where('heure_debut', $heureDebut)
                    ->exists();

                if (!$existe) {
                    CreneauRendezVous::create([
                        'agence_id' => $agence->id,
                        'date' => $date->format('Y-m-d'),
                        'heure_debut' => $heureDebut,
                        'heure_fin' => $heureFin,
                        'est_disponible' => true,
                    ]);
                    $creneauxCrees++;
                }
            }
        }

        return $creneauxCrees;
    }

    public function genererCreneauxAuto(Request $request)
    {
        $agence = Auth::user()->agence;

        $creneauxCrees = $this->genererCreneauxDepuisPlanning($agence, 30);

        if ($creneauxCrees === 0) {
            return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
                ->with('info', 'Les créneaux des 30 prochains jours sont déjà à jour.');
        }

        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', "{$creneauxCrees} créneaux générés automatiquement pour les 30 prochains jours.");
    }

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
            'Lun' => 0, 'Mar' => 1, 'Mer' => 2, 'Jeu' => 3,
            'Ven' => 4, 'Sam' => 5, 'Dim' => 6
        ];

        $heures = $request->heures;
        $joursSelectionnes = $request->jours;
        $estRecurrent = $request->boolean('est_recurrent', false);

        if ($estRecurrent) {
            \App\Models\PlanningType::where('agence_id', $agence->id)->delete();
            foreach ($joursSelectionnes as $jourFr) {
                $jourNum = $joursMap[$jourFr] ?? 0;
                foreach ($heures as $heure) {
                    $heureDebut = Carbon::parse($heure);
                    $heureFin = $heureDebut->copy()->addHour();

                    \App\Models\PlanningType::create([
                        'agence_id' => $agence->id,
                        'jour' => $jourNum,
                        'heure_debut' => $heureDebut->format('H:i'),
                        'heure_fin' => $heureFin->format('H:i'),
                    ]);
                }
            }

            $creneauxCrees = $this->genererCreneauxDepuisPlanning($agence, 30);

            return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
                ->with('success', "Planning type récurrent sauvegardé ! {$creneauxCrees} créneaux générés pour les 30 prochains jours.");
        } else {
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

    public function getPlanningType()
    {
        $agence = Auth::user()->agence;
        $formatted = \App\Models\PlanningType::getPlanningFormatted($agence->id);
        return response()->json($formatted);
    }

    private function getSemaine(): array
    {
        $semaine = [];
        for ($i = 0; $i < 7; $i++) {
            $semaine[] = Carbon::today()->addDays($i)->format('Y-m-d');
        }
        return $semaine;
    }

    // ==================== CRÉNEAUX ====================

    public function supprimerCreneau(CreneauRendezVous $creneau)
    {
        if ($creneau->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $creneau->delete();

        return redirect()->route('agence.profil', ['onglet' => 'creneaux'])
            ->with('success', 'Créneau supprimé avec succès.');
    }

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
 * Liste des rendez-vous - actifs + récemment clôturés
 */
public function rendezvous()
{
    $agence = Auth::user()->agence;

    // ✅ Inclure les RDV terminés et client_absent pour que l'agence
    //    VOIE ce qu'elle vient de valider/marquer
    $rendezVous = RendezVous::with(['proposition.demande', 'proposition.bien', 'particulier.user'])
        ->where('agence_id', $agence->id)
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value,
            StatutRendezVousEnum::TERMINE->value,        // ✅ AJOUT
            StatutRendezVousEnum::CLIENT_ABSENT->value,  // ✅ AJOUT
        ])
        // ✅ Trier : RDV actifs d'abord, puis les clôturés
        ->orderByRaw("
            CASE 
                WHEN statut IN ('planifie', 'confirme') THEN 0 
                ELSE 1 
            END
        ")
        ->orderBy('date_visite', 'asc')
        ->paginate(10);

    // ✅ Stats globales : tous les RDV de l'agence
    $baseQuery = RendezVous::where('agence_id', $agence->id);

    $stats = [
        'total'    => (clone $baseQuery)->count(),
        'planifie' => (clone $baseQuery)->where('statut', StatutRendezVousEnum::PLANIFIE->value)->count(),
        'confirme' => (clone $baseQuery)->where('statut', StatutRendezVousEnum::CONFIRME->value)->count(),
        'termine'  => (clone $baseQuery)->where('statut', StatutRendezVousEnum::TERMINE->value)->count(),
    ];

    $abonnementActif = config('abonnement.actif', false);
    $modeGratuit = config('abonnement.mode_gratuit', true);

    if (!$abonnementActif || $modeGratuit) {
        $abonnementActuel = (object) [
            'formule' => (object) [
                'value' => 'pro',
                'label' => 'Pro (gratuit)',
            ],
            'date_fin' => now()->addYears(10),
            'statut' => true,
        ];
    } else {
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
    }

    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();

    $rendezvousAVenir = $agence->rendezVous()
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE,
            StatutRendezVousEnum::CONFIRME
        ])
        ->where('date_visite', '>=', now()->toDateString())
        ->count();

    $notifData = $this->getNotifications();

    return view('agence.rendezvous.index', array_merge(compact(
        'rendezVous',
        'stats',
        'abonnementActuel',
        'besoinsDisponibles',
        'rendezvousAVenir',
        'abonnementActif',
        'modeGratuit'
    ), $notifData));
}

    public function rendezvousShow(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        $rendezVous->load(['proposition.bien', 'proposition.demande', 'particulier.user']);

        $notifData = $this->getNotifications();

        return view('agence.rendezvous.show', array_merge(compact('rendezVous'), $notifData));
    }

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

        return redirect()->route('agence.rendezvous.index')->with('success', $message);
    }

    /**
     * Confirmer un rendez-vous
     */
   public function rendezvousConfirmer(RendezVous $rendezVous)
{
    if ($rendezVous->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    $statutValue = is_object($rendezVous->statut) ? $rendezVous->statut->value : $rendezVous->statut;

    if ($statutValue !== StatutRendezVousEnum::PLANIFIE->value) {
        return back()->with('error', 'Seul un rendez-vous planifié peut être confirmé.');
    }

    try {
        DB::beginTransaction();

        $rendezVous->update(['statut' => StatutRendezVousEnum::CONFIRME->value]);

        try {
            if ($rendezVous->particulier?->user) {
                $rendezVous->particulier->user->notify(
                    new RendezVousConfirmeNotification($rendezVous)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification confirmation: ' . $e->getMessage());
        }

        DB::commit();

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous confirmé avec succès.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur confirmation RDV: ' . $e->getMessage());
        return redirect()->route('agence.rendezvous.index')
            ->with('error', 'Une erreur est survenue.');
    }
}

    /**
     * Annuler un rendez-vous
     */
/**
 * ✅ Annuler un rendez-vous (avant la visite)
 * RDV → ANNULE, proposition → en_attente, demande → en_attente
 * Les propositions refusées automatiquement sont réactivées.
 */
public function rendezvousAnnuler(RendezVous $rendezVous)
{
    if ($rendezVous->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    $statutValue = is_object($rendezVous->statut)
        ? $rendezVous->statut->value
        : (string) $rendezVous->statut;

    if ($statutValue === StatutRendezVousEnum::TERMINE->value) {
        return back()->with('error', 'Impossible d\'annuler une visite déjà terminée.');
    }

    if (in_array($statutValue, [
        StatutRendezVousEnum::ANNULE->value,
        StatutRendezVousEnum::CLIENT_ABSENT->value,
    ])) {
        return back()->with('error', 'Ce rendez-vous est déjà clôturé.');
    }

    if ($rendezVous->code_valide) {
        return back()->with('error', 'Impossible : la visite a déjà été validée.');
    }

    try {
        DB::beginTransaction();

        // ✅ 1. Libérer le créneau
        if ($rendezVous->creneau_id) {
            $creneau = CreneauRendezVous::find($rendezVous->creneau_id);
            if ($creneau) {
                $creneau->update(['est_disponible' => true]);
            }
        }

        // ✅ 2. RDV → annulé
        $rendezVous->update(['statut' => StatutRendezVousEnum::ANNULE->value]);

        // ✅ 3. Reset proposition + demande + autres propositions
        $proposition = $rendezVous->proposition;
        if ($proposition) {
            $demande = $proposition->demande;

            // Demande → en_attente
            if ($demande && $demande->statut === StatutDemandeEnum::EN_COURS->value) {
                $demande->update(['statut' => StatutDemandeEnum::EN_ATTENTE->value]);
            }

            // Proposition → en_attente
            $proposition->update([
                'statut'            => StatutPropositionEnum::EN_ATTENTE->value,
                'refus_automatique' => false,
            ]);

            // Réactiver les autres propositions refusées automatiquement
            Proposition::where('demande_id', $proposition->demande_id)
                ->where('id', '!=', $proposition->id)
                ->where('statut', StatutPropositionEnum::REFUSEE->value)
                ->where('refus_automatique', true)
                ->update([
                    'statut'            => StatutPropositionEnum::EN_ATTENTE->value,
                    'refus_automatique' => false,
                ]);
        }

        // ✅ 4. Notifier le particulier
        try {
            if ($rendezVous->particulier?->user) {
                $rendezVous->particulier->user->notify(
                    new RendezVousAnnuleNotification($rendezVous)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification annulation: ' . $e->getMessage());
        }

        DB::commit();

        return redirect()->route('agence.rendezvous.index')
            ->with('success', 'Rendez-vous annulé. Le particulier peut choisir une nouvelle offre.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur annulation RDV: ' . $e->getMessage());
        return redirect()->route('agence.rendezvous.index')
            ->with('error', 'Une erreur est survenue.');
    }
}

    /**
     * ✅ NOUVEAU : Marquer une visite comme effectuée
     * Déclenche le bouton "Évaluer l'agence" côté particulier
     */
    /**
 * ✅ Valider la visite avec le code fourni par le particulier
 */

/**
 * ✅ Valider la visite avec le code fourni par le particulier
 */
public function validerCode(Request $request, RendezVous $rendezVous)
{
    if ($rendezVous->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    $statutValue = is_object($rendezVous->statut)
        ? $rendezVous->statut->value
        : (string) $rendezVous->statut;

    if (!in_array($statutValue, [
        StatutRendezVousEnum::PLANIFIE->value,
        StatutRendezVousEnum::CONFIRME->value,
    ])) {
        return redirect()
            ->route('agence.rendezvous.index')
            ->with('error', 'Ce rendez-vous ne peut pas être validé (statut actuel : ' . $statutValue . ').');
    }

    $request->validate([
        'code_visite' => 'required|string|size:6|regex:/^[0-9]{6}$/',
    ], [
        'code_visite.required' => 'Veuillez saisir le code de visite.',
        'code_visite.size'     => 'Le code doit contenir exactement 6 chiffres.',
        'code_visite.regex'    => 'Le code doit être composé uniquement de 6 chiffres.',
    ]);

    if (!$rendezVous->verifierCode($request->code_visite)) {
        Log::warning('Code visite invalide', [
            'rendez_vous_id' => $rendezVous->id,
            'agence_id'      => Auth::user()->agence->id,
        ]);
        return redirect()
            ->route('agence.rendezvous.index')
            ->with('error', 'Code de visite incorrect. Vérifiez auprès du client.');
    }

    if ($rendezVous->code_valide) {
        return redirect()
            ->route('agence.rendezvous.index')
            ->with('error', 'Ce code a déjà été utilisé.');
    }

    try {
        DB::beginTransaction();

        // ✅ Valider la visite → statut TERMINE + code_valide = true
        $rendezVous->validerVisite($request->code_visite);

        // ✅ Clôturer la proposition gagnante + toutes les autres + la demande
        $proposition = $rendezVous->proposition;

        if ($proposition) {
            // 1. La proposition gagnante passe à TERMINEE
            $proposition->update([
                'statut' => StatutPropositionEnum::TERMINEE->value,
            ]);

            // 2. Toutes les autres propositions de la demande → REFUSEE
            Proposition::where('demande_id', $proposition->demande_id)
                ->where('id', '!=', $proposition->id)
                ->whereIn('statut', [
                    StatutPropositionEnum::EN_ATTENTE->value,
                    StatutPropositionEnum::ACCEPTEE->value,
                ])
                ->update([
                    'statut'            => StatutPropositionEnum::REFUSEE->value,
                    'refus_automatique' => false,
                ]);

            // 3. La demande passe à TERMINEE
            $demande = $proposition->demande;
            if ($demande && in_array($demande->statut, [
                StatutDemandeEnum::EN_ATTENTE->value,
                StatutDemandeEnum::EN_COURS->value,
            ])) {
                $demande->update([
                    'statut' => StatutDemandeEnum::TERMINEE->value,
                ]);
            }
        }

        // ✅ Notifier le particulier
        try {
            if ($rendezVous->particulier?->user) {
                $rendezVous->particulier->user->notify(
                    new RendezVousEffectueNotification($rendezVous)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification validation: ' . $e->getMessage());
        }

        DB::commit();

        // ✅ Redirection explicite + message de succès
        return redirect()
            ->route('agence.rendezvous.index')
            ->with('success', 'Visite validée avec succès ! La demande est clôturée. Le particulier peut maintenant évaluer votre agence.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur validation code: ' . $e->getMessage());

        return redirect()
            ->route('agence.rendezvous.index')
            ->with('error', 'Une erreur est survenue lors de la validation du code.');
    }
}
    /**
     * ✅ NOUVEAU : Marquer le client comme absent
     */
   /**
 * Marquer le client comme absent
 */
/**
 * ✅ Marquer le client comme absent
 * Le RDV passe à CLIENT_ABSENT, la proposition et la demande restent inchangées.
 * Le particulier est notifié et pourra replanifier sur la même proposition.
 */
public function rendezvousAbsent(RendezVous $rendezVous)
{
    if ($rendezVous->agence_id !== Auth::user()->agence->id) {
        abort(403);
    }

    $statutValue = is_object($rendezVous->statut)
        ? $rendezVous->statut->value
        : (string) $rendezVous->statut;

    if (!in_array($statutValue, [
        StatutRendezVousEnum::PLANIFIE->value,
        StatutRendezVousEnum::CONFIRME->value,
    ])) {
        return back()->with('error', 'Ce rendez-vous ne peut pas être marqué comme absent.');
    }

    if ($rendezVous->code_valide) {
        return back()->with('error', 'Impossible : la visite a déjà été validée par le code.');
    }

    try {
        DB::beginTransaction();

        // ✅ Libérer le créneau
        if ($rendezVous->creneau_id) {
            $creneau = CreneauRendezVous::find($rendezVous->creneau_id);
            if ($creneau) {
                $creneau->update(['est_disponible' => true]);
            }
        }

        // ✅ Passer le RDV à CLIENT_ABSENT
        $rendezVous->update([
            'statut' => StatutRendezVousEnum::CLIENT_ABSENT->value,
        ]);

        // ✅ Notifier le particulier
        try {
            if ($rendezVous->particulier?->user) {
                $rendezVous->particulier->user->notify(
                    new RendezVousClientAbsentNotification($rendezVous)
                );
            }
        } catch (\Exception $e) {
            Log::error('Erreur notification absent: ' . $e->getMessage());
        }

        DB::commit();

        return back()->with('success', 'Visite marquée : client absent. Le particulier a été notifié.');

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Erreur marquage absent: ' . $e->getMessage());
        return back()->with('error', 'Une erreur est survenue.');
    }
}


    /**
     * Marquer un rendez-vous comme terminé (ancien système)
     * Clôture aussi la proposition et la demande
     */
    public function rendezvousTermine(RendezVous $rendezVous)
    {
        if ($rendezVous->agence_id !== Auth::user()->agence->id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $rendezVous->update([
                'statut' => StatutRendezVousEnum::TERMINE->value,
            ]);

            $proposition = $rendezVous->proposition;
            if ($proposition) {
                $proposition->update([
                    'statut' => StatutPropositionEnum::TERMINEE->value,
                ]);

                $demande = $proposition->demande;
                if ($demande && $demande->statut === StatutDemandeEnum::EN_COURS->value) {
                    $demande->update([
                        'statut' => StatutDemandeEnum::TERMINEE->value,
                    ]);
                }
            }

            // Notifier le particulier
            try {
                if ($rendezVous->particulier?->user) {
                    $rendezVous->particulier->user->notify(
                        new RendezVousEffectueNotification($rendezVous)
                    );
                }
            } catch (\Exception $e) {
                Log::error('Erreur notification termine: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('agence.rendezvous.index')
                ->with('success', 'Rendez-vous terminé avec succès ! La demande est maintenant clôturée.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la clôture du rendez-vous: ' . $e->getMessage());
            return redirect()->route('agence.rendezvous.index')
                ->with('error', 'Une erreur est survenue lors de la clôture du rendez-vous.');
        }
    }

    // ==================== ÉVALUATIONS ====================

    public function evaluations()
    {
        $agence = Auth::user()->agence;

        $evaluations = Evaluation::with(['particulier.user', 'proposition.bien'])
            ->where('agence_id', $agence->id)
            ->whereNull('reponse_agence')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $avisTraites = Evaluation::where('agence_id', $agence->id)
            ->whereNotNull('reponse_agence')
            ->count();

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
            'avisTraites',
            'abonnementActuel',
            'besoinsDisponibles',
            'rendezvousAVenir'
        ), $notifData));
    }

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

    public function abonnement()
    {
        $agence = Auth::user()->agence;

        if (!$agence) {
            return redirect()->route('agence.dashboard')->with('error', 'Agence non trouvée.');
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
                'aDejaEuGratuit' => false,
                'isBasicActif' => false,
                'isProActif' => false,
            ], $notifData));
        }

        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();

        $aDejaEuGratuit = $agence->abonnements()
            ->where('formule', 'basic')
            ->where('statut', false)
            ->exists();

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

        $tarifsVedette = [
            1 => 1000, 3 => 1500, 7 => 3000, 14 => 5000, 30 => 8000,
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
            'aDejaEuGratuit',
            'isBasicActif',
            'isProActif'
        ), $notifData));
    }

    public function souscrire(Request $request)
    {
        try {
            $request->validate(['formule' => 'required|in:basic,pro']);

            $agence = Auth::user()->agence;

            if (!$agence) {
                return redirect()->route('agence.dashboard')->with('error', 'Agence non trouvée.');
            }

            if (!$agence->statut_validation) {
                return redirect()->route('agence.abonnement')
                    ->with('error', 'Votre agence doit être validée par un administrateur pour souscrire à un abonnement.');
            }

            $formule = FormuleAbonnementEnum::from($request->formule);
            $montant = $formule->prix();

            $abonnementActuel = $agence->abonnements()
                ->where('statut', true)
                ->where('date_fin', '>', now())
                ->first();

            if ($abonnementActuel) {
                $formuleActuelle = $abonnementActuel->formule->value;
                $formuleDemandee = $request->formule;

                if ($formuleActuelle === $formuleDemandee) {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà un abonnement ' . $formule->label() . ' actif jusqu\'au ' . $abonnementActuel->date_fin->format('d/m/Y') . '.');
                }

                if ($formuleActuelle === 'basic' && $formuleDemandee === 'pro') {
                    $abonnementActuel->update(['statut' => false]);
                } elseif ($formuleActuelle === 'pro') {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous avez déjà un abonnement Pro actif jusqu\'au ' . $abonnementActuel->date_fin->format('d/m/Y') . '.');
                } else {
                    return redirect()->route('agence.abonnement')
                        ->with('error', 'Vous ne pouvez pas changer d\'abonnement pour le moment.');
                }
            }

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

            $abonnement = Abonnement::create([
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

            return redirect()->route('agence.abonnement')->with('success', '🎉 Abonnement gratuit activé avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur souscription: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de la souscription: ' . $e->getMessage());
        }
    }

    public function upgrade(Request $request)
    {
        try {
            $request->validate(['formule' => 'required|in:basic,premium,pro']);

            $agence = Auth::user()->agence;

            if (!$agence) {
                return redirect()->route('agence.dashboard')->with('error', 'Agence non trouvée.');
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

            return redirect()->route('agence.abonnement')->with('success', 'Abonnement mis à jour avec succès !');
        } catch (\Exception $e) {
            Log::error('Erreur mise à jour abonnement: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')
                ->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function annuler(Abonnement $abonnement)
    {
        try {
            $agence = Auth::user()->agence;

            if ($abonnement->agence_id != $agence->id) {
                return redirect()->route('agence.abonnement')->with('error', 'Action non autorisée.');
            }

            $abonnement->update([
                'statut' => false,
                'paydunya_status' => 'cancelled',
            ]);

            return redirect()->route('agence.abonnement')->with('success', 'Abonnement annulé avec succès.');
        } catch (\Exception $e) {
            Log::error('Erreur annulation: ' . $e->getMessage());
            return redirect()->route('agence.abonnement')->with('error', 'Erreur lors de l\'annulation.');
        }
    }

    // ==================== HISTORIQUE ====================

   public function historique()
{
    $agence = Auth::user()->agence;

    // ═══════════════════════════════════════════════════════════
    // 1. PROPOSITIONS clôturées (terminee / refusee)
    // ═══════════════════════════════════════════════════════════
    $propositions = $agence->propositions()
        ->with(['demande', 'bien.medias'])
        ->whereIn('statut', [
            StatutPropositionEnum::TERMINEE->value,
            StatutPropositionEnum::REFUSEE->value,
        ])
        ->orderBy('updated_at', 'desc')
        ->get()
        ->map(function ($item) {
            $statutValue = is_object($item->statut)
                ? $item->statut->value
                : (string) $item->statut;

            return [
                'type'         => 'proposition',
                'titre'        => 'Offre ' . (is_object($item->statut) ? $item->statut->label() : ucfirst($statutValue)),
                'description'  => ($item->demande->type_bien->label() ?? 'Bien') . ' — ' . number_format($item->prix_propose, 0, ',', ' ') . ' FCFA',
                'date'         => $item->updated_at, // ✅ date de clôture
                'statut'       => is_object($item->statut) ? $item->statut->label() : ucfirst($statutValue),
                'statut_class' => $this->getStatusClass($statutValue),
                'medias'       => $item->bien ? $item->bien->medias : collect(),
                'details'      => [
                    'Bien'             => $item->bien->titre ?? 'N/A',
                    'Surface'          => ($item->bien->surface ?? 0) . ' m²',
                    'Prix proposé'     => number_format($item->prix_propose, 0, ',', ' ') . ' FCFA',
                    'Demande ID'       => '#' . $item->demande_id,
                    'Type de demande'  => $item->demande->type_bien->label() ?? 'N/A',
                    'Zone recherchée'  => $item->demande->zone_recherchee ?? 'N/A',
                    'Budget client'    => number_format($item->demande->budget_maximum, 0, ',', ' ') . ' FCFA',
                ],
                'link'         => route('agence.propositions.show', $item),
                'demande_link' => $item->demande
                    ? route('agence.demandes.show', ['demande' => $item->demande->slug])
                    : null,
            ];
        });

    // ═══════════════════════════════════════════════════════════
    // 2. RENDEZ-VOUS passés (termine / annule / client_absent)
    // ═══════════════════════════════════════════════════════════
    $rendezVous = $agence->rendezVous()
        ->with(['proposition.bien.medias', 'particulier.user', 'proposition.demande'])
        ->whereIn('statut', [
            StatutRendezVousEnum::TERMINE->value,
            StatutRendezVousEnum::ANNULE->value,
            StatutRendezVousEnum::CLIENT_ABSENT->value, // ✅ AJOUT
        ])
        ->orderBy('updated_at', 'desc')
        ->get()
        ->map(function ($item) {
            $statutValue = is_object($item->statut)
                ? $item->statut->value
                : (string) $item->statut;

            // Titre adapté selon le statut
            $titre = match ($statutValue) {
                StatutRendezVousEnum::TERMINE->value       => 'Visite effectuée',
                StatutRendezVousEnum::ANNULE->value        => 'Rendez-vous annulé',
                StatutRendezVousEnum::CLIENT_ABSENT->value => 'Client absent',
                default                                    => 'Rendez-vous',
            };

            return [
                'type'         => 'rendezvous',
                'titre'        => $titre,
                'description'  => ($item->proposition->bien->titre ?? 'Bien') . ' — ' . ($item->particulier->user->prenom ?? 'Client'),
                'date'         => $item->updated_at, // ✅ date de dernière action
                'statut'       => is_object($item->statut) ? $item->statut->label() : ucfirst($statutValue),
                'statut_class' => $this->getStatusClass($statutValue),
                'medias'       => $item->proposition->bien ? $item->proposition->bien->medias : collect(),
                'details'      => [
                    'Bien'        => $item->proposition->bien->titre ?? 'N/A',
                    'Date visite' => $item->date_visite ? $item->date_visite->format('d/m/Y') : '—',
                    'Heure'       => $item->heure_visite ?? '—',
                    'Client'      => $item->particulier->user->prenom ?? 'N/A',
                ],
                'link'         => route('agence.rendezvous.show', $item),
            ];
        });

    // ═══════════════════════════════════════════════════════════
    // 3. ÉVALUATIONS TRAITÉES (avec réponse de l'agence uniquement)
    // ═══════════════════════════════════════════════════════════
    $evaluations = $agence->evaluations()
        ->with(['particulier.user', 'proposition.bien.medias', 'proposition.demande'])
        ->whereNotNull('reponse_agence') // ✅ Option A
        ->orderBy('date_reponse', 'desc')
        ->get()
        ->map(function ($item) {
            return [
                'type'         => 'evaluation',
                'titre'        => 'Avis traité',
                'description'  => ($item->particulier->user->prenom ?? 'Client') . ' ' . ($item->particulier->user->nom ?? '') . ' — ' . $item->note . '/5',
                'date'         => $item->date_reponse ?? $item->updated_at,
                'statut'       => $item->note . ' ★',
                'statut_class' => $item->note >= 4 ? 'success' : ($item->note >= 3 ? 'warning' : 'danger'),
                'medias'       => $item->proposition && $item->proposition->bien
                    ? $item->proposition->bien->medias
                    : collect(),
                'details'      => [
                    'Note'        => $item->note . '/5',
                    'Client'      => ($item->particulier->user->prenom ?? 'Client') . ' ' . ($item->particulier->user->nom ?? ''),
                    'Commentaire' => $item->commentaire ?? 'Aucun commentaire',
                    'Réponse'     => $item->reponse_agence ?? 'Aucune',
                ],
                'link'         => route('agence.evaluations.show', $item),
            ];
        });

    // ═══════════════════════════════════════════════════════════
    // 4. FUSION + TRI + PAGINATION
    // ═══════════════════════════════════════════════════════════
    $activites = $propositions
        ->concat($rendezVous)
        ->concat($evaluations)
        ->sortByDesc('date')
        ->values();

    // ✅ Filtre optionnel par type (?type=proposition|rendezvous|evaluation)
    $typeFilter = request('type', '');
    if (!empty($typeFilter)) {
        $activites = $activites
            ->where('type', $typeFilter)
            ->values();
    }

    $perPage     = (int) request()->get('per_page', 15);
    $currentPage = (int) request()->get('page', 1);
    $offset      = ($currentPage - 1) * $perPage;
    $items       = $activites->slice($offset, $perPage)->values();
    $total       = $activites->count();

    $activites = new \Illuminate\Pagination\LengthAwarePaginator(
        $items,
        $total,
        $perPage,
        $currentPage,
        ['path' => request()->url(), 'query' => request()->query()]
    );

    // ═══════════════════════════════════════════════════════════
    // 5. DONNÉES ANNEXES (abonnement, stats)
    // ═══════════════════════════════════════════════════════════
    $abonnementActif = config('abonnement.actif', false);
    $modeGratuit     = config('abonnement.mode_gratuit', true);

    if (!$abonnementActif || $modeGratuit) {
        $abonnementActuel = (object) [
            'formule' => (object) [
                'value' => 'pro',
                'label' => 'Pro (gratuit)',
            ],
            'date_fin' => now()->addYears(10),
            'statut'   => true,
        ];
    } else {
        $abonnementActuel = $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
    }

    $besoinsDisponibles = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE)->count();

    $rendezvousAVenir = $agence->rendezVous()
        ->whereIn('statut', [
            StatutRendezVousEnum::PLANIFIE->value,
            StatutRendezVousEnum::CONFIRME->value,
        ])
        ->where('date_visite', '>=', now()->toDateString())
        ->count();

    $notifData = $this->getNotifications();

    return view('agence.historique', array_merge(compact(
        'activites',
        'abonnementActuel',
        'besoinsDisponibles',
        'rendezvousAVenir',
        'abonnementActif',
        'modeGratuit'
    ), $notifData));
}

/**
 * Retourne la classe CSS correspondant à un statut
 */
private function getStatusClass($status): string
{
    // Si enum, récupérer la valeur
    if (is_object($status) && method_exists($status, 'value')) {
        $status = $status->value;
    }

    $map = [
        // Propositions
        'en_attente'    => 'warning',
        'acceptee'      => 'success',
        'refusee'       => 'danger',
        'terminee'      => 'success',

        // Demandes
        'en_cours'      => 'info',
        'annulee'       => 'danger',

        // Rendez-vous
        'planifie'      => 'warning',
        'confirme'      => 'info',
        'termine'       => 'success',
        'annule'        => 'danger',
        'client_absent' => 'muted',

        // Évaluations
        'positif'       => 'success',
        'neutre'        => 'default',
    ];

    return $map[$status] ?? 'default';
}

    // ==================== UTILITAIRES ====================

    private function abonnementActif(): bool
    {
        return config('abonnement.actif', false);
    }

    private function modeGratuit(): bool
    {
        return config('abonnement.mode_gratuit', true);
    }

    private function getAbonnementActuel($agence)
    {
        if (!$this->abonnementActif() || $this->modeGratuit()) {
            return (object) [
                'formule' => (object) [
                    'value' => 'pro',
                    'label' => 'Pro (gratuit)',
                    'limiteOffres' => function () { return PHP_INT_MAX; },
                    'prixMensuel' => function () { return '0 FCFA (offert)'; },
                ],
                'date_fin' => now()->addYears(10),
                'statut' => true,
                'formule' => 'pro',
            ];
        }

        return $agence->abonnements()
            ->where('statut', true)
            ->where('date_fin', '>', now())
            ->first();
    }
}
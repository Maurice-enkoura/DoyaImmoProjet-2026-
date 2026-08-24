<?php

namespace App\Http\Controllers\Agence;

use App\Http\Controllers\Controller;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\TypeBienEnum;
use App\Enums\TypeOperationEnum;

class DashboardController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;

        // Abonnement
        $abonnementActuel = $agence->abonnementActif;
        $offresUtilisees = $agence->offresEnvoyeesMois ?? 0;
        
        $offresRestantes = 0;
        $pourcentageOffres = 0;
        $peutEnvoyerOffres = false;
        
        if ($abonnementActuel) {
            $limite = $abonnementActuel->formule->limiteOffres();
            
            if ($limite === PHP_INT_MAX) {
                $offresRestantes = 999999;
                $peutEnvoyerOffres = true;
                $pourcentageOffres = 0;
            } else {
                $offresRestantes = max(0, $limite - $offresUtilisees);
                $peutEnvoyerOffres = $offresRestantes > 0;
                $pourcentageOffres = $limite > 0 ? round(($offresUtilisees / $limite) * 100) : 0;
            }
        }

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
            
            // ✅ Pour le KPI "actifs" (besoins en cours)
            'actifs' => DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_COURS->value)->count(),
        ];

        // Derniers besoins disponibles
        $derniersBesoins = DemandeImmobiliere::where('statut', StatutDemandeEnum::EN_ATTENTE->value)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($besoin) {
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
            ->map(function($rdv) {
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

        return view('agence.dashboard', compact(
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
            'peutEnvoyerOffres'
        ));
    }
}
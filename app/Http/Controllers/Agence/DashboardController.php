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

class DashboardController extends Controller
{
    public function index()
    {
        $agence = Auth::user()->agence;

        // Abonnement
        $abonnementActuel = $agence->abonnementActif;
        $offresUtilisees = $agence->offresEnvoyeesMois ?? 0;
        
        // Calcul des offres restantes avec gestion de l'illimité
        $offresRestantes = 0;
        $pourcentageOffres = 0;
        $peutEnvoyerOffres = false;
        
        if ($abonnementActuel) {
            $limite = $abonnementActuel->formule->limiteBiens();
            
            if ($limite === PHP_INT_MAX) {
                // Illimité
                $offresRestantes = 999999; // Un grand nombre pour l'affichage
                $peutEnvoyerOffres = true;
                $pourcentageOffres = 0;
            } else {
                $offresRestantes = max(0, $limite - $offresUtilisees);
                $peutEnvoyerOffres = $offresRestantes > 0;
                $pourcentageOffres = $limite > 0 ? round(($offresUtilisees / $limite) * 100) : 0;
            }
        }

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
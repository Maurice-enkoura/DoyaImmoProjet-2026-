<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agence;
use App\Models\DocumentAgence;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\Signalement;
use App\Models\Abonnement;
use App\Models\Evaluation;
use App\Models\BienImmobilier;
use App\Models\RendezVous;
use App\Models\Quartier;
use Illuminate\Http\Request;
use App\Enums\StatutDocumentEnum;
use App\Enums\StatutSignalementEnum;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Statistiques globales
        $stats = [
            'total_users' => User::count(),
            'users_par_role' => User::selectRaw('role, count(*) as total')->groupBy('role')->get(),
            'users_evolution' => $this->calculateEvolution(User::class),
            
            'agences' => [
                'total' => Agence::count(),
                'en_attente' => Agence::where('statut_validation', false)->count(),
                'validees' => Agence::where('statut_validation', true)->count(),
                // Supprimer 'bloquees' car la colonne n'existe pas
                // Si vous avez besoin de cette statistique, ajoutez d'abord la colonne via une migration
            ],
            'agences_evolution' => $this->calculateEvolution(Agence::class),
            
            'documents' => [
                'total' => DocumentAgence::count(),
                'en_attente' => DocumentAgence::where('statut_validation', StatutDocumentEnum::EN_ATTENTE)->count(),
                'valides' => DocumentAgence::where('statut_validation', StatutDocumentEnum::VALIDE)->count(),
                'rejetes' => DocumentAgence::where('statut_validation', StatutDocumentEnum::REJETE)->count(),
            ],
            
            'demandes' => [
                'total' => DemandeImmobiliere::count(),
                'en_attente' => DemandeImmobiliere::where('statut', 'en_attente')->count(),
                'en_cours' => DemandeImmobiliere::where('statut', 'en_cours')->count(),
                'terminees' => DemandeImmobiliere::where('statut', 'terminee')->count(),
                'annulees' => DemandeImmobiliere::where('statut', 'annulee')->count(),
            ],
            
            'propositions' => [
                'total' => Proposition::count(),
                'en_attente' => Proposition::where('statut', 'en_attente')->count(),
                'acceptees' => Proposition::where('statut', 'acceptee')->count(),
                'refusees' => Proposition::where('statut', 'refusee')->count(),
            ],
            
            'rendezvous' => [
                'total' => RendezVous::count(),
                'planifies' => RendezVous::where('statut', 'planifie')->count(),
                'confirmes' => RendezVous::where('statut', 'confirme')->count(),
                'termines' => RendezVous::where('statut', 'termine')->count(),
                'annules' => RendezVous::where('statut', 'annule')->count(),
            ],
            
            'signalements' => [
                'total' => Signalement::count(),
                'en_attente' => Signalement::where('statut', StatutSignalementEnum::EN_ATTENTE)->count(),
                'traites' => Signalement::where('statut', StatutSignalementEnum::TRAITE)->count(),
                'rejetes' => Signalement::where('statut', StatutSignalementEnum::REJETE)->count(),
            ],
            
            'abonnements' => [
                'total' => Abonnement::count(),
                'actifs' => Abonnement::where('statut', true)->where('date_fin', '>', now())->count(),
                'expires' => Abonnement::where('statut', false)->orWhere('date_fin', '<=', now())->count(),
                'par_formule' => Abonnement::where('statut', true)
                    ->selectRaw('formule, count(*) as total')
                    ->groupBy('formule')
                    ->get(),
            ],
            
            'evaluations' => [
                'total' => Evaluation::count(),
                'note_moyenne' => Evaluation::avg('note') ?? 0,
                'top_agences' => Agence::withAvg('evaluations', 'note')
                    ->having('evaluations_avg_note', '>', 0)
                    ->orderBy('evaluations_avg_note', 'desc')
                    ->limit(5)
                    ->get(),
            ],
            
            'biens' => [
                'total' => BienImmobilier::count(),
                'disponibles' => BienImmobilier::where('statut', true)->count(),
                'par_type' => BienImmobilier::selectRaw('type_bien, count(*) as total')
                    ->groupBy('type_bien')
                    ->get(),
            ],
            
            'quartiers' => [
                'total' => Quartier::count(),
                'actifs' => Quartier::where('est_actif', true)->count(),
            ],
            
            'inscriptions' => User::selectRaw('DATE(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        // Variables pour le layout (badges et listes récentes)
        $agencesEnAttente = Agence::where('statut_validation', false)->count();
        $signalementsEnAttente = Signalement::where('statut', StatutSignalementEnum::EN_ATTENTE)->count();
        
        // Derniers utilisateurs inscrits
        $derniersUtilisateurs = User::with(['particulier', 'agence', 'administrateur'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Derniers signalements
        $derniersSignalements = Signalement::with(['particulier.user'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Agences en attente de validation
        $agencesEnAttenteList = Agence::with(['user', 'quartier'])
            ->where('statut_validation', false)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Derniers biens publiés
        $derniersBiens = BienImmobilier::with(['agence.user', 'quartier'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Dernières demandes
        $dernieresDemandes = DemandeImmobiliere::with(['particulier.user', 'quartier'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'agencesEnAttente',
            'signalementsEnAttente',
            'derniersUtilisateurs',
            'derniersSignalements',
            'agencesEnAttenteList',
            'derniersBiens',
            'dernieresDemandes'
        ));
    }

    /**
     * Calcule l'évolution en pourcentage sur les 30 derniers jours
     */
    private function calculateEvolution($model)
    {
        try {
            $total = $model::count();
            if ($total === 0) return 0;
            
            $ancienTotal = $model::where('created_at', '<', now()->subDays(30))->count();
            if ($ancienTotal === 0) return 100;
            
            return round((($total - $ancienTotal) / $ancienTotal) * 100);
        } catch (\Exception $e) {
            return 0;
        }
    }
}
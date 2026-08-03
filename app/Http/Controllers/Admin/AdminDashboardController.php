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
use Illuminate\Http\Request;
use App\Enums\StatutDocumentEnum;
use App\Enums\StatutSignalementEnum;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'users_par_role' => User::selectRaw('role, count(*) as total')->groupBy('role')->get(),
            
            'agences' => [
                'total' => Agence::count(),
                'en_attente' => Agence::where('statut_validation', false)->count(),
                'validees' => Agence::where('statut_validation', true)->count(),
            ],
            
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
            
            'signalements' => [
                'total' => Signalement::count(),
                'en_attente' => Signalement::where('statut', StatutSignalementEnum::EN_ATTENTE)->count(),
                'traites' => Signalement::where('statut', StatutSignalementEnum::TRAITE)->count(),
                'rejetes' => Signalement::where('statut', StatutSignalementEnum::REJETE)->count(),
            ],
            
            'abonnements' => [
                'total' => Abonnement::count(),
                'actifs' => Abonnement::where('statut', true)->where('date_fin', '>', now())->count(),
                'expires' => Abonnement::where('statut', false)->count(),
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
            
            'inscriptions' => User::selectRaw('DATE(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
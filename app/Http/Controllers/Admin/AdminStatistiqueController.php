<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\Signalement;
use App\Models\Abonnement;
use App\Models\Evaluation;
use App\Models\BienImmobilier;
use Illuminate\Http\Request;

class AdminStatistiqueController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'par_role' => User::selectRaw('role, count(*) as total')
                    ->groupBy('role')
                    ->get(),
                'evolution' => User::selectRaw('DATE(created_at) as date, count(*) as total')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ],

            'agences' => [
                'total' => Agence::count(),
                'validees' => Agence::where('statut_validation', true)->count(),
                'en_attente' => Agence::where('statut_validation', false)->count(),
                'evolution' => Agence::selectRaw('DATE(created_at) as date, count(*) as total')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ],

            'demandes' => [
                'total' => DemandeImmobiliere::count(),
                'par_statut' => DemandeImmobiliere::selectRaw('statut, count(*) as total')
                    ->groupBy('statut')
                    ->get(),
                'par_type_operation' => DemandeImmobiliere::selectRaw('type_operation, count(*) as total')
                    ->groupBy('type_operation')
                    ->get(),
                'par_type_bien' => DemandeImmobiliere::selectRaw('type_bien, count(*) as total')
                    ->groupBy('type_bien')
                    ->get(),
                'evolution' => DemandeImmobiliere::selectRaw('DATE(created_at) as date, count(*) as total')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ],

            'propositions' => [
                'total' => Proposition::count(),
                'par_statut' => Proposition::selectRaw('statut, count(*) as total')
                    ->groupBy('statut')
                    ->get(),
                'prix_moyen' => Proposition::avg('prix_propose') ?? 0,
                'prix_min' => Proposition::min('prix_propose') ?? 0,
                'prix_max' => Proposition::max('prix_propose') ?? 0,
                'evolution' => Proposition::selectRaw('DATE(created_at) as date, count(*) as total')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ],

            'biens' => [
                'total' => BienImmobilier::count(),
                'disponibles' => BienImmobilier::where('statut', true)->count(),
                'par_type' => BienImmobilier::selectRaw('type_bien, count(*) as total')
                    ->groupBy('type_bien')
                    ->get(),
                'par_contrat' => BienImmobilier::selectRaw('type_contrat, count(*) as total')
                    ->groupBy('type_contrat')
                    ->get(),
                'prix_moyen' => BienImmobilier::avg('prix') ?? 0,
                'surface_moyenne' => BienImmobilier::avg('surface') ?? 0,
                'evolution' => BienImmobilier::selectRaw('DATE(created_at) as date, count(*) as total')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ],

            'signalements' => [
                'total' => Signalement::count(),
                'par_statut' => Signalement::selectRaw('statut, count(*) as total')
                    ->groupBy('statut')
                    ->get(),
                'par_motif' => Signalement::selectRaw('motif, count(*) as total')
                    ->groupBy('motif')
                    ->get(),
                'evolution' => Signalement::selectRaw('DATE(created_at) as date, count(*) as total')
                    ->where('created_at', '>=', now()->subMonths(6))
                    ->groupBy('date')
                    ->orderBy('date')
                    ->get(),
            ],

            'abonnements' => [
                'total' => Abonnement::count(),
                'actifs' => Abonnement::where('statut', true)->where('date_fin', '>', now())->count(),
                'par_formule' => Abonnement::selectRaw('formule, count(*) as total')
                    ->groupBy('formule')
                    ->get(),
                'revenus' => Abonnement::sum('montant') ?? 0,
                'revenus_par_formule' => Abonnement::selectRaw('formule, sum(montant) as total')
                    ->groupBy('formule')
                    ->get(),
            ],

            'evaluations' => [
                'total' => Evaluation::count(),
                'note_moyenne' => Evaluation::avg('note') ?? 0,
                'note_min' => Evaluation::min('note') ?? 0,
                'note_max' => Evaluation::max('note') ?? 0,
                'repartition_notes' => Evaluation::selectRaw('note, count(*) as total')
                    ->groupBy('note')
                    ->orderBy('note')
                    ->get(),
                'top_agences' => Agence::withAvg('evaluations', 'note')
                    ->withCount('evaluations')
                    ->having('evaluations_avg_note', '>', 0)
                    ->orderBy('evaluations_avg_note', 'desc')
                    ->limit(10)
                    ->get(),
            ],

            'activite' => [
                'aujourd_hui' => User::whereDate('created_at', today())->count(),
                'cette_semaine' => User::whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count(),
                'ce_mois' => User::whereMonth('created_at', now()->month)->count(),
            ],
        ];

        return view('admin.statistiques', compact('stats'));
    }

    public function export(Request $request)
    {
        $type = $request->type;
        $format = $request->format ?? 'csv';

        // Logique d'export selon le type et le format
        // ...

        return redirect()->back()->with('success', 'Export en cours de préparation.');
    }
}
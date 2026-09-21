<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Agence;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Signalement;
use App\Models\Abonnement;
use App\Models\Evaluation;
use App\Models\BienImmobilier;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;

class AdminStatistiqueController extends Controller
{
    /**
     * Statistiques globales de la plateforme
     * Les données sont mises en cache 5 minutes
     */
    public function index()
    {
        $stats = Cache::remember('admin_statistiques', 300, function () {
            return $this->buildStats();
        });

        return view('admin.statistiques', compact('stats'));
    }

    /**
     * Construit toutes les statistiques
     */
    private function buildStats(): array
    {
        return [
            'users' => $this->statsUsers(),
            'agences' => $this->statsAgences(),
            'demandes' => $this->statsDemandes(),
            'propositions' => $this->statsPropositions(),
            'biens' => $this->statsBiens(),
            'rendezvous' => $this->statsRendezVous(),
            'signalements' => $this->statsSignalements(),
            'abonnements' => $this->statsAbonnements(),
            'evaluations' => $this->statsEvaluations(),
            'activite' => $this->statsActivite(),
        ];
    }

    // ============================================================
    // USERS
    // ============================================================
    private function statsUsers(): array
    {
        return [
            'total' => User::count(),
            'par_role' => User::selectRaw('role, count(*) as total')
                ->groupBy('role')
                ->get(),
            'evolution' => User::selectRaw('DATE(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    // ============================================================
    // AGENCES
    // ============================================================
    private function statsAgences(): array
    {
        return [
            'total' => Agence::count(),
            'validees' => Agence::where('statut_validation', true)->count(),
            'en_attente' => Agence::where('statut_validation', false)->count(),
            'bloquees' => Agence::where('bloque', true)->count(),
            'refusees' => Agence::where('est_refusee', true)->count(),
            'evolution' => Agence::selectRaw('DATE(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    // ============================================================
    // DEMANDES
    // ============================================================
    private function statsDemandes(): array
    {
        return [
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
        ];
    }

    // ============================================================
    // PROPOSITIONS
    // ============================================================
    private function statsPropositions(): array
    {
        return [
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
        ];
    }

    // ============================================================
    // BIENS
    // ============================================================
    private function statsBiens(): array
    {
        return [
            'total' => BienImmobilier::count(),
            'disponibles' => BienImmobilier::where('statut', true)->count(),
            'en_vedette' => BienImmobilier::where('est_vedette', true)->count(),
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
        ];
    }

    // ============================================================
    // RENDEZ-VOUS (nouveau)
    // ============================================================
    private function statsRendezVous(): array
    {
        return [
            'total' => RendezVous::count(),
            'par_statut' => RendezVous::selectRaw('statut, count(*) as total')
                ->groupBy('statut')
                ->get(),
            'planifies' => RendezVous::where('statut', StatutRendezVousEnum::PLANIFIE->value)->count(),
            'confirmes' => RendezVous::where('statut', StatutRendezVousEnum::CONFIRME->value)->count(),
            'termines' => RendezVous::where('statut', StatutRendezVousEnum::TERMINE->value)->count(),
            'annules' => RendezVous::where('statut', StatutRendezVousEnum::ANNULE->value)->count(),
            'client_absent' => RendezVous::where('statut', StatutRendezVousEnum::CLIENT_ABSENT->value)->count(),
            'taux_presence' => $this->calculerTauxPresence(),
            'evolution' => RendezVous::selectRaw('DATE(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subMonths(6))
                ->groupBy('date')
                ->orderBy('date')
                ->get(),
        ];
    }

    /**
     * Taux de présence (visites terminées / total RDV clôturés)
     */
    private function calculerTauxPresence(): float
    {
        $totalClotures = RendezVous::whereIn('statut', [
            StatutRendezVousEnum::TERMINE->value,
            StatutRendezVousEnum::CLIENT_ABSENT->value,
            StatutRendezVousEnum::ANNULE->value,
        ])->count();

        if ($totalClotures === 0) {
            return 0;
        }

        $termines = RendezVous::where('statut', StatutRendezVousEnum::TERMINE->value)->count();

        return round(($termines / $totalClotures) * 100, 1);
    }

    // ============================================================
    // SIGNALEMENTS
    // ============================================================
    private function statsSignalements(): array
    {
        return [
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
        ];
    }

    // ============================================================
    // ABONNEMENTS
    // ============================================================
    private function statsAbonnements(): array
    {
        return [
            'total' => Abonnement::count(),
            'actifs' => Abonnement::where('statut', true)
                ->where('date_fin', '>', now())
                ->count(),
            'par_formule' => Abonnement::selectRaw('formule, count(*) as total')
                ->groupBy('formule')
                ->get(),
            'revenus' => Abonnement::sum('montant') ?? 0,
            'revenus_par_formule' => Abonnement::selectRaw('formule, sum(montant) as total')
                ->groupBy('formule')
                ->get(),
        ];
    }

    // ============================================================
    // ÉVALUATIONS
    // ============================================================
    private function statsEvaluations(): array
    {
        return [
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
                ->havingRaw('evaluations_count > 0')
                ->orderByDesc('evaluations_avg_note')
                ->limit(10)
                ->get(),
        ];
    }

    // ============================================================
    // ACTIVITÉ RÉCENTE
    // ============================================================
    private function statsActivite(): array
    {
        return [
            'aujourd_hui' => User::whereDate('created_at', today()->toDateString())->count(),
            'cette_semaine' => User::whereBetween('created_at', [
                now()->startOfWeek(),
                now()->endOfWeek(),
            ])->count(),
            'ce_mois' => User::whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->count(),
        ];
    }

    /**
     * Export des statistiques en CSV
     */
    public function export(Request $request)
    {
        $type = $request->get('type', 'global');
        $format = $request->get('format', 'csv');

        // Rafraîchir les stats (pas de cache pour l'export)
        $stats = $this->buildStats();

        switch ($type) {
            case 'users':
                return $this->exportUsers();
            case 'agences':
                return $this->exportAgences();
            case 'propositions':
                return $this->exportPropositions();
            case 'rendezvous':
                return $this->exportRendezVous();
            case 'global':
            default:
                return $this->exportGlobal($stats);
        }
    }

    private function exportUsers()
    {
        $filename = 'utilisateurs_' . now()->format('Y-m-d') . '.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nom', 'Prénom', 'Email', 'Rôle', 'Créé le'], ';');

            User::chunk(500, function ($users) use ($handle) {
                foreach ($users as $user) {
                    fputcsv($handle, [
                        $user->id,
                        $user->nom,
                        $user->prenom,
                        $user->email,
                        is_object($user->role) ? $user->role->value : $user->role,
                        $user->created_at->format('d/m/Y H:i'),
                    ], ';');
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportAgences()
    {
        $filename = 'agences_' . now()->format('Y-m-d') . '.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Nom', 'Email', 'Adresse', 'Validée', 'Créée le'], ';');

            Agence::with('user')->chunk(500, function ($agences) use ($handle) {
                foreach ($agences as $agence) {
                    fputcsv($handle, [
                        $agence->id,
                        $agence->nom_agence,
                        $agence->user->email ?? '—',
                        $agence->adresse,
                        $agence->statut_validation ? 'Oui' : 'Non',
                        $agence->created_at->format('d/m/Y H:i'),
                    ], ';');
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportPropositions()
    {
        $filename = 'propositions_' . now()->format('Y-m-d') . '.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Agence', 'Particulier', 'Prix proposé', 'Statut', 'Créée le'], ';');

            Proposition::with(['agence', 'particulier.user'])->chunk(500, function ($props) use ($handle) {
                foreach ($props as $p) {
                    fputcsv($handle, [
                        $p->id,
                        $p->agence->nom_agence ?? '—',
                        trim(($p->particulier->user->prenom ?? '') . ' ' . ($p->particulier->user->nom ?? '')),
                        $p->prix_propose,
                        is_object($p->statut) ? $p->statut->value : $p->statut,
                        $p->created_at->format('d/m/Y H:i'),
                    ], ';');
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportRendezVous()
    {
        $filename = 'rendezvous_' . now()->format('Y-m-d') . '.csv';

        $callback = function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['ID', 'Agence', 'Particulier', 'Date visite', 'Heure', 'Code', 'Validé', 'Statut'], ';');

            RendezVous::with(['agence', 'particulier.user'])->chunk(500, function ($rdvs) use ($handle) {
                foreach ($rdvs as $rdv) {
                    fputcsv($handle, [
                        $rdv->id,
                        $rdv->agence->nom_agence ?? '—',
                        trim(($rdv->particulier->user->prenom ?? '') . ' ' . ($rdv->particulier->user->nom ?? '')),
                        $rdv->date_visite ? $rdv->date_visite->format('d/m/Y') : '—',
                        $rdv->heure_visite ?? '—',
                        $rdv->code_visite ?? '—',
                        $rdv->code_valide ? 'Oui' : 'Non',
                        is_object($rdv->statut) ? $rdv->statut->value : $rdv->statut,
                    ], ';');
                }
            });

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function exportGlobal(array $stats)
    {
        $filename = 'statistiques_globales_' . now()->format('Y-m-d') . '.csv';

        $callback = function () use ($stats) {
            $handle = fopen('php://output', 'w');

            // Utilisateurs
            fputcsv($handle, ['Utilisateurs', $stats['users']['total']], ';');
            fputcsv($handle, ['Agences', $stats['agences']['total']], ';');
            fputcsv($handle, ['Demandes', $stats['demandes']['total']], ';');
            fputcsv($handle, ['Propositions', $stats['propositions']['total']], ';');
            fputcsv($handle, ['Biens', $stats['biens']['total']], ';');
            fputcsv($handle, ['Rendez-vous', $stats['rendezvous']['total']], ';');
            fputcsv($handle, ['Signalements', $stats['signalements']['total']], ';');
            fputcsv($handle, ['Évaluations', $stats['evaluations']['total']], ';');

            fclose($handle);
        };

        return Response::streamDownload($callback, $filename, [
            'Content-Type' => 'text/csv',
        ]);
    }
}
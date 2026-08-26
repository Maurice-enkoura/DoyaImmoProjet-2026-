<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use App\Models\Signalement;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HistoriqueController extends Controller
{
    public function index()
    {
        $particulier = Auth::user()->particulier;
        $events = [];

        // ✅ Demandes - UNIQUEMENT terminées ou annulées
        $demandes = DemandeImmobiliere::where('particulier_id', $particulier->id)
            ->whereIn('statut', [
                StatutDemandeEnum::TERMINEE->value,
                StatutDemandeEnum::ANNULEE->value
            ])
            ->with(['propositions.agence', 'propositions.bien.medias'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($demandes as $demande) {
            // ✅ Récupérer la proposition acceptée ou terminée
            $proposition = $demande->propositions
                ->whereIn('statut', [
                    StatutPropositionEnum::ACCEPTEE->value,
                    StatutPropositionEnum::TERMINEE->value
                ])
                ->first();

            // ✅ Si aucune proposition acceptée/terminée, prendre la première (pour l'affichage)
            if (!$proposition) {
                $proposition = $demande->propositions->first();
            }

            $evaluation = null;

            // ✅ Si une proposition existe, récupérer l'évaluation associée
            if ($proposition) {
                $evaluation = Evaluation::where('proposition_id', $proposition->id)
                    ->where('particulier_id', $particulier->id)
                    ->first();
            }

            $events[] = [
                'type' => 'demande',
                'title' => 'Demande ' . $demande->statut->label(),
                'description' => $demande->type_bien->label() . ' — ' . $demande->zone_recherchee,
                'date' => $demande->created_at->format('d/m/Y à H:i'),
                'icon' => 'fa-regular fa-house-circle-check',
                'color' => '#4A90D9',
                'status' => $demande->statut->label(),
                'status_class' => $this->getStatusClass($demande->statut->value),
                'link' => route('particulier.demandes.show', $demande),
                'medias' => $proposition && $proposition->bien ? $proposition->bien->medias : collect(),
                'details' => [
                    'Demande' => '#' . $demande->id,
                    'Type de bien' => $demande->type_bien->label(),
                    'Zone' => $demande->zone_recherchee,
                    'Budget' => number_format($demande->budget_maximum, 0, ',', ' ') . ' FCFA',
                ],
                'proposition' => $proposition ? [
                    'id' => $proposition->id,
                    'agence' => $proposition->agence->nom_agence ?? 'N/A',
                    'prix' => number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA',
                    'statut' => $proposition->statut->label(),
                    'statut_class' => $this->getStatusClass($proposition->statut->value),
                    'link' => route('particulier.propositions.show', $proposition),
                ] : null,
                'evaluation' => $evaluation ? [
                    'note' => $evaluation->note,
                    'commentaire' => $evaluation->commentaire,
                    'link' => route('particulier.evaluations.show', $evaluation),
                ] : null,
            ];
        }

        // Trier par date
        usort($events, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        // Pagination manuelle
        $perPage = 15;
        $currentPage = request()->get('page', 1);
        $offset = ($currentPage - 1) * $perPage;
        $items = array_slice($events, $offset, $perPage);
        $total = count($events);

        $historique = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('particulier.historique', compact('historique'));
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
            'positif' => 'success',
            'neutre' => 'default',
        ];

        return $map[$status] ?? 'default';
    }
}
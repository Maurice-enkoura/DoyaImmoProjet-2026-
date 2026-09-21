<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Pagination\LengthAwarePaginator;

class HistoriqueController extends Controller
{
    public function index()
    {
        $particulier = Auth::user()->particulier;
        $events = [];

        // ═══════════════════════════════════════════════════════════
        // 1. DEMANDES clôturées (terminee / annulee)
        // ═══════════════════════════════════════════════════════════
        $demandes = DemandeImmobiliere::where('particulier_id', $particulier->id)
            ->whereIn('statut', [
                StatutDemandeEnum::TERMINEE->value,
                StatutDemandeEnum::ANNULEE->value,
            ])
            ->with(['propositions.agence', 'propositions.bien.medias'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($demandes as $demande) {
            // Proposition gagnante = acceptee ou terminee
            $proposition = $demande->propositions
                ->whereIn('statut', [
                    StatutPropositionEnum::ACCEPTEE,
                    StatutPropositionEnum::TERMINEE,
                ])
                ->first();

            // Évaluation éventuelle
            $evaluation = null;
            if ($proposition) {
                $evaluation = Evaluation::where('proposition_id', $proposition->id)
                    ->where('particulier_id', $particulier->id)
                    ->first();
            }

            $events[] = [
                'type'          => 'demande',
                'title'         => 'Demande ' . $demande->statut->label(),
                'description'   => $demande->type_bien->label() . ' — ' . $demande->zone_recherchee,
                'date_sort'     => $demande->updated_at->timestamp,
                'date_label'    => $demande->updated_at->format('d/m/Y à H:i'),
                'relative'      => $demande->updated_at->diffForHumans(),
                'icon'          => 'fa-regular fa-house-circle-check',
                'color'         => '#4A90D9',
                'status'        => $demande->statut->label(),
                'status_class'  => $this->getStatusClass($demande->statut->value),
                'link'          => route('particulier.demandes.show', $demande),
                'medias'        => $proposition && $proposition->bien
                    ? $proposition->bien->medias
                    : collect(),
                'details'       => [
                    'Demande'      => '#' . $demande->id,
                    'Type de bien' => $demande->type_bien->label(),
                    'Zone'         => $demande->zone_recherchee,
                    'Budget'       => number_format($demande->budget_maximum, 0, ',', ' ') . ' FCFA',
                ],
                'proposition'   => $proposition ? [
                    'id'           => $proposition->id,
                    'agence'       => $proposition->agence->nom_agence ?? 'N/A',
                    'prix'         => number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA',
                    'statut'       => $proposition->statut->label(),
                    'statut_class' => $this->getStatusClass($proposition->statut->value),
                    'link'         => route('particulier.propositions.show', $proposition),
                ] : null,
                'evaluation'    => $evaluation ? [
                    'note'         => $evaluation->note,
                    'commentaire'  => $evaluation->commentaire,
                    'link'         => route('particulier.evaluations.show', $evaluation),
                ] : null,
            ];
        }

        // ═══════════════════════════════════════════════════════════
        // 2. RENDEZ-VOUS passés (termine / annule / client_absent)
        // ═══════════════════════════════════════════════════════════
        $rendezVous = RendezVous::where('particulier_id', $particulier->id)
            ->whereIn('statut', [
                StatutRendezVousEnum::TERMINE->value,
                StatutRendezVousEnum::ANNULE->value,
                StatutRendezVousEnum::CLIENT_ABSENT->value,
            ])
            ->with(['proposition.bien.medias', 'proposition.agence', 'agence'])
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($rendezVous as $rdv) {
            $bien   = $rdv->proposition->bien ?? null;
            $agence = $rdv->agence ?? ($rdv->proposition->agence ?? null);

            $statutValue = is_object($rdv->statut)
                ? $rdv->statut->value
                : (string) $rdv->statut;

            // Mapping icône/couleur/titre
            [$icon, $color, $title] = match ($statutValue) {
                StatutRendezVousEnum::TERMINE->value       => ['fa-solid fa-circle-check',      '#1E7A47', 'Visite effectuée'],
                StatutRendezVousEnum::ANNULE->value        => ['fa-solid fa-ban',                '#C62828', 'Rendez-vous annulé'],
                StatutRendezVousEnum::CLIENT_ABSENT->value => ['fa-solid fa-user-slash',         '#8E8E93', 'Visite non honorée'],
                default                                    => ['fa-regular fa-calendar',        '#8E8E93', 'Rendez-vous'],
            };

            $events[] = [
                'type'          => 'rendezvous',
                'title'         => $title,
                'description'   => ($bien->titre ?? 'Bien') . ' — ' . ($agence->nom_agence ?? 'Agence'),
                'date_sort'     => $rdv->updated_at->timestamp,
                'date_label'    => $rdv->updated_at->format('d/m/Y à H:i'),
                'relative'      => $rdv->updated_at->diffForHumans(),
                'icon'          => $icon,
                'color'         => $color,
                'status'        => $rdv->statut->label(),
                'status_class'  => $this->getStatusClass($statutValue),
                'link'          => route('particulier.rendezvous.show', $rdv),
                'medias'        => $bien ? $bien->medias : collect(),
                'details'       => [
                    'Bien'        => $bien->titre ?? 'N/A',
                    'Agence'      => $agence->nom_agence ?? 'N/A',
                    'Date visite' => $rdv->date_visite ? $rdv->date_visite->format('d/m/Y') : '—',
                    'Heure'       => $rdv->heure_visite ?? '—',
                ],
                'proposition'   => null,
                'evaluation'    => null,
            ];
        }

        // ═══════════════════════════════════════════════════════════
        // 3. ÉVALUATIONS postées (indépendantes)
        // ═══════════════════════════════════════════════════════════
        $evaluations = Evaluation::where('particulier_id', $particulier->id)
            ->with(['proposition.bien.medias', 'proposition.agence', 'agence'])
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($evaluations as $eval) {
            $agence = $eval->agence ?? ($eval->proposition->agence ?? null);
            $bien   = $eval->proposition->bien ?? null;

            $events[] = [
                'type'          => 'evaluation',
                'title'         => 'Évaluation postée',
                'description'   => ($agence->nom_agence ?? 'Agence') . ' — ' . $eval->note . '/5',
                'date_sort'     => $eval->created_at->timestamp,
                'date_label'    => $eval->created_at->format('d/m/Y à H:i'),
                'relative'      => $eval->created_at->diffForHumans(),
                'icon'          => 'fa-solid fa-star',
                'color'         => '#F5A623',
                'status'        => $eval->note . ' ★',
                'status_class'  => $eval->note >= 4 ? 'success' : ($eval->note >= 3 ? 'warning' : 'danger'),
                'link'          => route('particulier.evaluations.show', $eval),
                'medias'        => $bien ? $bien->medias : collect(),
                'details'       => [
                    'Agence'      => $agence->nom_agence ?? 'N/A',
                    'Note'        => $eval->note . '/5',
                    'Commentaire' => $eval->commentaire ?? 'Aucun',
                ],
                'proposition'   => null,
                'evaluation'    => null,
            ];
        }

        // ═══════════════════════════════════════════════════════════
        // TRI global par date (desc)
        // ═══════════════════════════════════════════════════════════
        usort($events, fn($a, $b) => $b['date_sort'] - $a['date_sort']);

        // ═══════════════════════════════════════════════════════════
        // PAGINATION manuelle
        // ═══════════════════════════════════════════════════════════
        $perPage     = 15;
        $currentPage = request()->get('page', 1);
        $offset      = ($currentPage - 1) * $perPage;
        $items       = array_slice($events, $offset, $perPage);
        $total       = count($events);

        $historique = new LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('particulier.historique', compact('historique'));
    }

    private function getStatusClass($status): string
    {
        // Si enum, récupérer la valeur
        if (is_object($status) && method_exists($status, 'value')) {
            $status = $status->value;
        }

        $map = [
            // Demandes
            'en_attente'    => 'warning',
            'en_cours'      => 'info',
            'terminee'      => 'success',
            'annulee'       => 'danger',

            // Propositions
            'acceptee'      => 'success',
            'refusee'       => 'danger',

            // Rendez-vous
            'planifie'      => 'warning',
            'confirme'      => 'info',
            'termine'       => 'success',
            'annule'        => 'danger',
            'client_absent' => 'muted',

            // Divers
            'positif'       => 'success',
            'neutre'        => 'default',
        ];

        return $map[$status] ?? 'default';
    }
}
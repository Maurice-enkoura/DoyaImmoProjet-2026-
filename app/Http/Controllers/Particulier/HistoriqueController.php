<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use App\Models\Signalement;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class HistoriqueController extends Controller
{
    public function index()
    {
        $particulier = Auth::user()->particulier;
        $events = [];

        // Demandes
        $demandes = DemandeImmobiliere::where('particulier_id', $particulier->id)
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($demandes as $demande) {
            $events[] = [
                'type' => 'demande',
                'title' => 'Demande publiée',
                'description' => $demande->type_bien->label() . ' — ' . $demande->zone_recherchee,
                'date' => $demande->created_at->format('d/m/Y à H:i'),
                'icon' => 'fa-regular fa-house-circle-check',
                'color' => '#4A90D9',
                'status' => $demande->statut->label(),
                'status_class' => $this->getStatusClass($demande->statut->value),
                'link' => route('particulier.demandes.show', $demande),
                'medias' => collect(), // Pas de médias pour les demandes
            ];
        }

        // Propositions (avec médias du bien)
        $propositions = Proposition::where('particulier_id', $particulier->id)
            ->with(['agence', 'bien.medias']) // Charger les médias du bien
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($propositions as $proposition) {
            $events[] = [
                'type' => 'proposition',
                'title' => 'Proposition reçue',
                'description' => $proposition->agence->nom_agence . ' — ' . number_format($proposition->prix_propose, 0, ',', ' ') . ' FCFA',
                'date' => $proposition->created_at->format('d/m/Y à H:i'),
                'icon' => 'fa-regular fa-file-invoice',
                'color' => '#F5A623',
                'status' => $proposition->statut->label(),
                'status_class' => $this->getStatusClass($proposition->statut->value),
                'link' => route('particulier.propositions.show', $proposition),
                'medias' => $proposition->bien ? $proposition->bien->medias : collect(), // ✅ Médias du bien
            ];
        }

        // Rendez-vous (avec médias du bien)
        $rendezVous = RendezVous::where('particulier_id', $particulier->id)
            ->with(['agence', 'proposition.bien.medias']) // Charger les médias du bien
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($rendezVous as $rdv) {
            $events[] = [
                'type' => 'rendezvous',
                'title' => 'Rendez-vous ' . $rdv->statut->label(),
                'description' => $rdv->agence->nom_agence . ' — ' . ($rdv->proposition->bien->titre ?? 'Visite'),
                'date' => $rdv->created_at->format('d/m/Y à H:i'),
                'icon' => 'fa-regular fa-calendar-days',
                'color' => '#0D47A1',
                'status' => $rdv->statut->label(),
                'status_class' => $this->getStatusClass($rdv->statut->value),
                'link' => route('particulier.rendezvous.show', $rdv),
                'medias' => $rdv->proposition->bien ? $rdv->proposition->bien->medias : collect(), // ✅ Médias du bien
            ];
        }

        // Évaluations (sans médias)
        $evaluations = Evaluation::where('particulier_id', $particulier->id)
            ->with('agence')
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($evaluations as $evaluation) {
            $events[] = [
                'type' => 'evaluation',
                'title' => 'Avis donné',
                'description' => $evaluation->agence->nom_agence . ' — ' . $evaluation->note . '/5',
                'date' => $evaluation->created_at->format('d/m/Y à H:i'),
                'icon' => 'fa-regular fa-star',
                'color' => '#1E7A47',
                'status' => $evaluation->note . '★',
                'status_class' => $evaluation->note >= 4 ? 'success' : 'default',
                'link' => route('particulier.evaluations.show', $evaluation),
                'medias' => collect(), // Pas de médias pour les évaluations
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
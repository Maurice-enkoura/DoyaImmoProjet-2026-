<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutRendezVousEnum;
use Illuminate\Support\Facades\Auth;

class ParticulierDashboardController extends Controller
{
    public function index()
    {
        $particulier = Auth::user()->particulier;

        // ✅ Variables pour le layout
        $abonnementActif  = config('abonnement.actif', false);
        $modeGratuit      = config('abonnement.mode_gratuit', true);
        $abonnementActuel = null;

        // ─────────────────────────────────────────────
        // 1️⃣  Statuts (values) réutilisables
        // ─────────────────────────────────────────────
        $statutsDemandeActifs = [
            StatutDemandeEnum::EN_ATTENTE->value,  // 'en_attente'
            StatutDemandeEnum::EN_COURS->value,    // 'en_cours'
        ];

        $statutsRdvActifs = [
            StatutRendezVousEnum::PLANIFIE->value, // 'planifie'
            StatutRendezVousEnum::CONFIRME->value, // 'confirme'
        ];

        // ─────────────────────────────────────────────
        // 2️⃣  IDs des besoins actifs
        // ─────────────────────────────────────────────
        $besoinsActifsIds = $particulier->demandes()
            ->whereIn('statut', $statutsDemandeActifs)
            ->pluck('id')
            ->toArray();

        // ─────────────────────────────────────────────
        // 3️⃣  Statistiques
        // ─────────────────────────────────────────────
        $stats = [
            'total_demandes' => $particulier->demandes()
                ->whereIn('statut', $statutsDemandeActifs)
                ->count(),

            'total_offres' => $particulier->propositions()
                ->where('statut', StatutPropositionEnum::EN_ATTENTE->value)
                ->whereIn('demande_id', $besoinsActifsIds)
                ->count(),

            'nouvelles_offres' => $particulier->propositions()
                ->where('statut', StatutPropositionEnum::EN_ATTENTE->value)
                ->whereIn('demande_id', $besoinsActifsIds)
                ->count(),

            'rendezvous_a_venir' => $particulier->rendezVous()
                ->whereIn('statut', $statutsRdvActifs)
                ->where('date_visite', '>=', now()->toDateString())
                ->count(),

            'total_evaluations' => $particulier->evaluations()->count(),

            'actifs' => $particulier->demandes()
                ->whereIn('statut', $statutsDemandeActifs)
                ->count(),
        ];

        // ─────────────────────────────────────────────
        // 4️⃣  Derniers besoins actifs (max 3)
        // ─────────────────────────────────────────────
        $derniersBesoins = $particulier->demandes()
            ->with('propositions')
            ->whereIn('statut', $statutsDemandeActifs)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function ($demande) {
                if (is_string($demande->statut)) {
                    $demande->statut = StatutDemandeEnum::from($demande->statut);
                }
                return $demande;
            });

        // ─────────────────────────────────────────────
        // 5️⃣  Prochain RDV + autres RDV
        // ─────────────────────────────────────────────
        $prochainRendezVous = $particulier->rendezVous()
            ->with(['proposition.bien', 'agence'])
            ->whereIn('statut', $statutsRdvActifs)
            ->where('date_visite', '>=', now()->toDateString())
            ->orderBy('date_visite', 'asc')
            ->orderBy('heure_visite', 'asc')
            ->first();

        $prochainsRendezVous = $particulier->rendezVous()
            ->with(['proposition.bien', 'agence'])
            ->whereIn('statut', $statutsRdvActifs)
            ->where('date_visite', '>=', now()->toDateString())
            ->when($prochainRendezVous, function ($query) use ($prochainRendezVous) {
                $query->where('id', '!=', $prochainRendezVous->id);
            })
            ->orderBy('date_visite', 'asc')
            ->orderBy('heure_visite', 'asc')
            ->limit(3)
            ->get();

        // ─────────────────────────────────────────────
        // 6️⃣  Meilleure offre (la plus récente)
        // ─────────────────────────────────────────────
        $meilleureOffre = $particulier->propositions()
            ->with(['agence', 'demande', 'bien.medias'])
            ->where('statut', StatutPropositionEnum::EN_ATTENTE->value)
            ->whereIn('demande_id', $besoinsActifsIds)
            ->orderByDesc('created_at')
            ->first();

        if ($meilleureOffre && is_string($meilleureOffre->statut)) {
            $meilleureOffre->statut = StatutPropositionEnum::from($meilleureOffre->statut);
        }

        $nouvellesOffresCount = $stats['nouvelles_offres'];

        // ─────────────────────────────────────────────
        // 7️⃣  Notifications
        // ─────────────────────────────────────────────
        $notifications      = Auth::user()->unreadNotifications()->limit(5)->get();
        $notificationsCount = Auth::user()->unreadNotifications()->count();

        // Compteurs sidebar
        $besoinsCount = $stats['total_demandes'];
        $offresCount  = $stats['total_offres'];

        return view('particulier.dashboard', compact(
            'stats',
            'derniersBesoins',
            'prochainRendezVous',
            'prochainsRendezVous',
            'meilleureOffre',
            'nouvellesOffresCount',
            'besoinsCount',
            'offresCount',
            'notificationsCount',
            'notifications',
            'abonnementActif',
            'modeGratuit',
            'abonnementActuel'
        ));
    }
}
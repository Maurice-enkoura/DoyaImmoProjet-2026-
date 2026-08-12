<?php

namespace App\Http\Controllers\Particulier;

use App\Http\Controllers\Controller;
use App\Models\Particulier;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Evaluation;
use Illuminate\Support\Facades\Auth;
use App\Enums\StatutDemandeEnum;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;

class ParticulierDashboardController extends Controller
{
    public function index()
    {
        $particulier = Auth::user()->particulier;

        // Statistiques
        $stats = [
            'total_demandes' => $particulier->demandes()
                ->whereIn('statut', [StatutDemandeEnum::EN_ATTENTE, StatutDemandeEnum::EN_COURS])
                ->count(),
            'total_offres' => $particulier->propositions()
                ->where('statut', StatutPropositionEnum::EN_ATTENTE)
                ->count(),
            'nouvelles_offres' => $particulier->propositions()
                ->where('statut', StatutPropositionEnum::EN_ATTENTE)
                ->count(),
            'rendezvous_a_venir' => $particulier->rendezVous()
                ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
                ->count(),
            'total_evaluations' => $particulier->evaluations()->count(),
            'actifs' => $particulier->demandes()
                ->whereIn('statut', [StatutDemandeEnum::EN_ATTENTE, StatutDemandeEnum::EN_COURS])
                ->count(),
        ];

        // Derniers besoins actifs (UNIQUEMENT EN_ATTENTE et EN_COURS)
        $derniersBesoins = $particulier->demandes()
            ->with('propositions')
            ->whereIn('statut', [StatutDemandeEnum::EN_ATTENTE, StatutDemandeEnum::EN_COURS])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get()
            ->map(function($demande) {
                // Convertir le statut en Enum si c'est une chaîne
                if (is_string($demande->statut)) {
                    $demande->statut = StatutDemandeEnum::from($demande->statut);
                }
                return $demande;
            });

        // Prochains rendez-vous
        $prochainsRendezVous = $particulier->rendezVous()
            ->with(['proposition.bien', 'agence'])
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->orderBy('date_visite', 'asc')
            ->limit(3)
            ->get()
            ->map(function($rdv) {
                // Convertir le statut en Enum si c'est une chaîne
                if (is_string($rdv->statut)) {
                    $rdv->statut = StatutRendezVousEnum::from($rdv->statut);
                }
                return $rdv;
            });

        // Prochain rendez-vous (singulier)
        $prochainRendezVous = $particulier->rendezVous()
            ->with(['proposition.bien', 'agence'])
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->orderBy('date_visite', 'asc')
            ->first();

        // Dernières offres en attente
        $dernieresOffres = $particulier->propositions()
            ->with(['agence', 'demande', 'bien'])
            ->where('statut', StatutPropositionEnum::EN_ATTENTE)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function($offre) {
                // Convertir le statut en Enum si c'est une chaîne
                if (is_string($offre->statut)) {
                    $offre->statut = StatutPropositionEnum::from($offre->statut);
                }
                return $offre;
            });

        // Notifications
        $notifications = Auth::user()->unreadNotifications()->limit(5)->get();
        $notificationsCount = Auth::user()->unreadNotifications()->count();

        // Messages
        $messages = [
            [
                'sender' => 'Teranga Immobilier',
                'preview' => 'Bonjour, nous avons une nouvelle offre pour vous...',
                'time' => 'Il y a 2h'
            ],
            [
                'sender' => 'Dakar Habitat',
                'preview' => 'Votre rendez-vous est confirmé pour demain...',
                'time' => 'Il y a 5h'
            ]
        ];
        $messagesCount = count($messages);

        // Pour le layout dashboard (sidebar)
        $besoinsCount = $particulier->demandes()
            ->whereIn('statut', [StatutDemandeEnum::EN_ATTENTE, StatutDemandeEnum::EN_COURS])
            ->count();
        $offresCount = $particulier->propositions()
            ->where('statut', StatutPropositionEnum::EN_ATTENTE)
            ->count();

        return view('particulier.dashboard', compact(
            'stats',
            'derniersBesoins',
            'prochainRendezVous',
            'prochainsRendezVous',
            'dernieresOffres',
            'besoinsCount',
            'offresCount',
            'notificationsCount',
            'notifications',
            'messagesCount',
            'messages'
        ));
    }
}
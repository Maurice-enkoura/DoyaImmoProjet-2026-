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

        // Derniers besoins actifs
        $derniersBesoins = $particulier->demandes()
            ->with('propositions')
            ->whereIn('statut', [StatutDemandeEnum::EN_ATTENTE, StatutDemandeEnum::EN_COURS])
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();

        // ✅ Prochains rendez-vous (pluriel - pour la liste)
        $prochainsRendezVous = $particulier->rendezVous()
            ->with(['proposition.bien', 'agence'])
            ->whereIn('statut', [StatutRendezVousEnum::PLANIFIE, StatutRendezVousEnum::CONFIRME])
            ->where('date_visite', '>=', now()->toDateString())
            ->orderBy('date_visite', 'asc')
            ->limit(3)
            ->get();

        // ✅ Prochain rendez-vous (singulier - pour l'affichage unique si besoin)
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
            ->get();

        // Notifications
        $notifications = Auth::user()->unreadNotifications()->limit(5)->get();
        $notificationsCount = Auth::user()->unreadNotifications()->count();

        // Messages (exemple statique pour le moment)
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
            'prochainsRendezVous', // ✅ Ajout de la variable
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
<?php

namespace App\Services;

use App\Models\User;
use App\Models\DemandeImmobiliere;
use App\Models\Proposition;
use App\Models\RendezVous;
use App\Models\Agence;
use App\Models\Abonnement;
use App\Notifications\NouvelleDemandeNotification;
use App\Notifications\NouvellePropositionNotification;
use App\Notifications\PropositionAccepteeNotification;
use App\Notifications\RendezVousConfirmeNotification;
use App\Notifications\AgenceValideeNotification;
use App\Notifications\AgenceRefuseeNotification;
use App\Notifications\AbonnementExpirantNotification;
use Illuminate\Support\Facades\Notification;
class NotificationService
{
    public function notifierNouvelleDemande(DemandeImmobiliere $demande): void
    {
        // Notifier toutes les agences validées dans la zone recherchée
        $agences = Agence::where('statut_validation', true)
            ->whereHas('user', function ($query) use ($demande) {
                // Optionnel: filtrer par zone
            })
            ->with('user')
            ->get();

        foreach ($agences as $agence) {
            $agence->user->notify(new NouvelleDemandeNotification($demande));
        }
    }

    public function notifierNouvelleProposition(Proposition $proposition): void
    {
        $particulier = $proposition->particulier;
        $particulier->user->notify(new NouvellePropositionNotification($proposition));
    }

    public function notifierPropositionAcceptee(Proposition $proposition): void
    {
        $agence = $proposition->agence;
        $agence->user->notify(new PropositionAccepteeNotification($proposition));
    }

    public function notifierRendezVousConfirme(RendezVous $rendezVous): void
    {
        $agence = $rendezVous->agence;
        $agence->user->notify(new RendezVousConfirmeNotification($rendezVous));
    }

    public function notifierAgenceValidee(Agence $agence): void
    {
        $agence->user->notify(new AgenceValideeNotification($agence));
    }

    public function notifierAgenceRefusee(Agence $agence, string $motif = null): void
    {
        $agence->user->notify(new AgenceRefuseeNotification($agence, $motif));
    }

    public function notifierAbonnementsExpirants(): void
    {
        $abonnements = Abonnement::where('statut', true)
            ->whereBetween('date_fin', [now(), now()->addDays(7)])
            ->with('agence.user')
            ->get();

        foreach ($abonnements as $abonnement) {
            if ($abonnement->agence && $abonnement->agence->user) {
                $abonnement->agence->user->notify(new AbonnementExpirantNotification($abonnement));
            }
        }
    }
}
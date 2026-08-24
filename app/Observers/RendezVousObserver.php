<?php

namespace App\Observers;

use App\Models\RendezVous;
use App\Models\Proposition;
use App\Models\DemandeImmobiliere;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutDemandeEnum;

class RendezVousObserver
{
    /**
     * Handle the RendezVous "updated" event.
     */
    public function updated(RendezVous $rendezVous): void
    {
        // Si le rendez-vous passe à "termine"
        if ($rendezVous->statut === StatutRendezVousEnum::TERMINE) {
            // Récupérer la proposition liée
            $proposition = $rendezVous->proposition;
            
            if ($proposition) {
                // Mettre à jour le statut de la proposition à "acceptee" si ce n'est pas déjà fait
                if ($proposition->statut !== StatutPropositionEnum::ACCEPTEE) {
                    $proposition->statut = StatutPropositionEnum::ACCEPTEE;
                    $proposition->save();
                }
                
                // Récupérer la demande liée
                $demande = $proposition->demande;
                
                // Si la demande est encore "en_cours", la passer à "terminee"
                if ($demande && $demande->statut === StatutDemandeEnum::EN_COURS) {
                    $demande->statut = StatutDemandeEnum::TERMINEE;
                    $demande->save();
                }
            }
        }
    }

    /**
     * Handle the RendezVous "created" event.
     */
    public function created(RendezVous $rendezVous): void
    {
        // Quand un rendez-vous est créé (planifié), mettre à jour le statut du besoin
        $proposition = $rendezVous->proposition;
        if ($proposition) {
            $demande = $proposition->demande;
            if ($demande && $demande->statut === StatutDemandeEnum::EN_ATTENTE) {
                $demande->statut = StatutDemandeEnum::EN_COURS;
                $demande->save();
            }
        }
    }
}
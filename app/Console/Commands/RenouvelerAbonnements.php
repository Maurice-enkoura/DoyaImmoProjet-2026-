<?php
// app/Console/Commands/RenouvelerAbonnements.php

namespace App\Console\Commands;

use App\Models\Abonnement;
use Illuminate\Console\Command;
use Carbon\Carbon;

class RenouvelerAbonnements extends Command
{
    protected $signature = 'abonnements:renouveler';
    protected $description = 'Renouvelle automatiquement les abonnements expirés';

    public function handle()
    {
        // Récupérer les abonnements expirés mais actifs
        $abonnements = Abonnement::where('statut', true)
            ->where('date_fin', '<', now())
            ->get();

        foreach ($abonnements as $abonnement) {
            // Vérifier si l'agence a un moyen de paiement
            // Si oui, renouveler automatiquement
            $abonnement->update([
                'date_debut' => now(),
                'date_fin' => now()->addMonth(),
            ]);
            
            $this->info(" Abonnement #{$abonnement->id} renouvelé");
        }

        $this->info("Renouvellement terminé");
        return Command::SUCCESS;
    }
}
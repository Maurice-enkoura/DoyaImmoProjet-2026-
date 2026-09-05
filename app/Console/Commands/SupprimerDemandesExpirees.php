<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DemandeImmobiliere;
use Illuminate\Support\Facades\Log;

class SupprimerDemandesExpirees extends Command
{
    protected $signature = 'demandes:supprimer-expirees';
    protected $description = 'Supprime automatiquement les demandes immobilières expirées (plus de 30 jours)';

    public function handle()
    {
        $count = DemandeImmobiliere::supprimerDemandesExpirees();
        
        if ($count > 0) {
            Log::info("{$count} demande(s) expirée(s) supprimée(s) automatiquement.");
            $this->info("{$count} demande(s) expirée(s) supprimée(s) avec succès !");
        } else {
            $this->info(" Aucune demande expirée à supprimer.");
        }
        
        return Command::SUCCESS;
    }
}
<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RendezVous;
use App\Models\Proposition;
use App\Enums\StatutRendezVousEnum;
use App\Enums\StatutPropositionEnum;
use App\Enums\StatutDemandeEnum;

class SyncRendezVousStatuts extends Command
{
    protected $signature = 'rendezvous:sync-statuts';
    protected $description = 'Synchronise les statuts des rendez-vous avec les propositions';

    public function handle()
    {
        $this->info('🔄 Synchronisation des statuts des rendez-vous...');

        // === 1. Rendez-vous terminés → Propositions terminées ===
        $rendezVousTermines = RendezVous::where('statut', StatutRendezVousEnum::TERMINE->value)->get();
        
        $countPropositions = 0;
        foreach ($rendezVousTermines as $rdv) {
            $proposition = $rdv->proposition;
            if ($proposition && $proposition->statut !== StatutPropositionEnum::TERMINEE->value) {
                $proposition->update([
                    'statut' => StatutPropositionEnum::TERMINEE->value
                ]);
                $countPropositions++;
                $this->line("   ✅ Proposition #{$proposition->id} mise à jour.");
            }
        }
        $this->info("✅ {$countPropositions} proposition(s) synchronisée(s).");

        // === 2. Propositions terminées → Rendez-vous terminés ===
        $this->info('🔍 Vérification des propositions terminées...');
        $propositionsTerminees = Proposition::where('statut', StatutPropositionEnum::TERMINEE->value)->get();
        
        $countRdv = 0;
        foreach ($propositionsTerminees as $proposition) {
            // ✅ Récupérer le premier rendez-vous lié
            $rdv = $proposition->rendezVous()->first();
            if ($rdv && $rdv->statut !== StatutRendezVousEnum::TERMINE->value) {
                $rdv->update([
                    'statut' => StatutRendezVousEnum::TERMINE->value
                ]);
                $countRdv++;
                $this->line("   ✅ Rendez-vous #{$rdv->id} mis à jour.");
            }
        }
        $this->info("✅ {$countRdv} rendez-vous synchronisé(s).");

        // === 3. Statistiques finales ===
        $this->info('📊 Statistiques finales:');
        $this->line("   - Rendez-vous terminés: " . RendezVous::where('statut', StatutRendezVousEnum::TERMINE->value)->count());
        $this->line("   - Propositions terminées: " . Proposition::where('statut', StatutPropositionEnum::TERMINEE->value)->count());
        
        return Command::SUCCESS;
    }
}
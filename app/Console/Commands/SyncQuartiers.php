<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Quartier;
use Illuminate\Support\Facades\DB;

class SyncQuartiers extends Command
{
    protected $signature = 'quartiers:sync';
    protected $description = 'Synchronise les quartiers par défaut de Dakar';

    public function handle()
    {
        $quartiers = [
            'Plateau', 'Médina', 'Gueule Tapée', 'Fass', 'Colobane', 'Hlm', 'Grand Dakar',
            'Almadies', 'Ngor', 'Ouakam', 'Yoff', 'Mermoz', 'Sacré-Cœur', 'Point E',
            'Fann', 'Hann', 'Parcelles Assainies', 'Grand Yoff', 'Cité Cej', 'Cité SOW',
            'Ouest Foire', 'Liberté', 'Mbao', 'Rufisque', 'Thiaroye', 'Yeumbeul',
            'Keur Massar', 'Malika', 'Diamniadio', 'Sébikotane', 'Bargny', 'Guédiawaye'
        ];

        $count = 0;
        foreach ($quartiers as $nom) {
            try {
                Quartier::firstOrCreate(
                    ['nom' => $nom],
                    [
                        'ville' => 'Dakar',
                        'est_actif' => true,
                    ]
                );
                $count++;
                $this->info("✓ Quartier '{$nom}' synchronisé");
            } catch (\Exception $e) {
                $this->error("✗ Erreur pour '{$nom}': " . $e->getMessage());
            }
        }

        $this->info("{$count} quartiers synchronisés avec succès.");
    }
}
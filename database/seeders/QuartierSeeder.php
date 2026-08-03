<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Quartier;

class QuartierSeeder extends Seeder
{
    public function run(): void
    {
        $quartiers = [
            // Dakar Plateau
            ['nom' => 'Plateau', 'ville' => 'Dakar'],
            ['nom' => 'Médina', 'ville' => 'Dakar'],
            ['nom' => 'Gueule Tapée', 'ville' => 'Dakar'],
            ['nom' => 'Fass', 'ville' => 'Dakar'],
            ['nom' => 'Colobane', 'ville' => 'Dakar'],
            ['nom' => 'Hlm', 'ville' => 'Dakar'],
            ['nom' => 'Grand Dakar', 'ville' => 'Dakar'],
            ['nom' => 'Dieuppeul', 'ville' => 'Dakar'],
            ['nom' => 'Derklé', 'ville' => 'Dakar'],
            
            // Almadies
            ['nom' => 'Almadies', 'ville' => 'Dakar'],
            ['nom' => 'Ngor', 'ville' => 'Dakar'],
            ['nom' => 'Ouakam', 'ville' => 'Dakar'],
            ['nom' => 'Yoff', 'ville' => 'Dakar'],
            ['nom' => 'Mermoz', 'ville' => 'Dakar'],
            ['nom' => 'Sacré-Cœur', 'ville' => 'Dakar'],
            ['nom' => 'Point E', 'ville' => 'Dakar'],
            ['nom' => 'Fann', 'ville' => 'Dakar'],
            ['nom' => 'Hann', 'ville' => 'Dakar'],
            ['nom' => 'Ameth Fall', 'ville' => 'Dakar'],
            
            // Parcelles Assainies
            ['nom' => 'Parcelles Assainies', 'ville' => 'Dakar'],
            ['nom' => 'Grand Yoff', 'ville' => 'Dakar'],
            ['nom' => 'Cité Cej', 'ville' => 'Dakar'],
            ['nom' => 'Cité SOW', 'ville' => 'Dakar'],
            ['nom' => 'Cité Mbow', 'ville' => 'Dakar'],
            ['nom' => 'Cité CUS', 'ville' => 'Dakar'],
            
            // Ouest Foire
            ['nom' => 'Ouest Foire', 'ville' => 'Dakar'],
            ['nom' => 'Liberté', 'ville' => 'Dakar'],
            ['nom' => 'Mbao', 'ville' => 'Dakar'],
            ['nom' => 'Rufisque', 'ville' => 'Dakar'],
            ['nom' => 'Pikine', 'ville' => 'Dakar'],
            
            // Autres
            ['nom' => 'Thiaroye', 'ville' => 'Dakar'],
            ['nom' => 'Yeumbeul', 'ville' => 'Dakar'],
            ['nom' => 'Keur Massar', 'ville' => 'Dakar'],
            ['nom' => 'Malika', 'ville' => 'Dakar'],
            ['nom' => 'Diamniadio', 'ville' => 'Dakar'],
            ['nom' => 'Sébikotane', 'ville' => 'Dakar'],
            ['nom' => 'Bargny', 'ville' => 'Dakar'],
            ['nom' => 'Golf Sud', 'ville' => 'Dakar'],
            ['nom' => 'Guédiawaye', 'ville' => 'Dakar'],
            ['nom' => 'Parcelles Assainies', 'ville' => 'Dakar'],
        ];

        foreach ($quartiers as $quartier) {
            Quartier::firstOrCreate(
                ['nom' => $quartier['nom']],
                [
                    'ville' => $quartier['ville'],
                    'est_actif' => true,
                ]
            );
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Signalement;
use App\Models\Agence;
use App\Models\BienImmobilier;
use App\Models\Particulier;
use Faker\Factory as FakerFactory;

class SignalementSeeder extends Seeder
{
    protected $faker;

    public function __construct()
    {
        $this->faker = FakerFactory::create('fr_FR');
    }

    public function run(): void
    {
        $this->command->info('Ajout de signalements supplémentaires...');

        $particuliers = Particulier::take(5)->get();
        $agences = Agence::where('statut_validation', true)->take(5)->get();

        foreach ($particuliers as $particulier) {
            $agence = $agences->random();
            $bien = $agence->biens()->inRandomOrder()->first();

            if ($bien) {
                Signalement::create([
                    'particulier_id' => $particulier->id,
                    'agence_id' => $agence->id,
                    'signalable_id' => $bien->id,
                    'signalable_type' => BienImmobilier::class,
                    'motif' => $this->faker->randomElement(['fraude', 'contenu_inapproprie', 'arnaque', 'fausse_annonce']),
                    'description' => $this->faker->paragraph,
                    'statut' => $this->faker->randomElement(['en_attente', 'traite', 'rejete']),
                    'date_signalement' => now(),
                ]);

                $this->command->info("✅ Signalement ajouté pour le bien: {$bien->titre}");
            }
        }
    }
}
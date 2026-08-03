<?php

namespace Database\Factories;

use App\Models\DemandeImmobiliere;
use App\Models\Particulier;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TypeOperationEnum;
use App\Enums\TypeBienEnum;
use App\Enums\StatutDemandeEnum;

class DemandeImmobiliereFactory extends Factory
{
    protected $model = DemandeImmobiliere::class;

    public function definition(): array
    {
        return [
            'particulier_id' => Particulier::factory(),
            'type_operation' => $this->faker->randomElement(TypeOperationEnum::cases()),
            'type_bien' => $this->faker->randomElement(TypeBienEnum::cases()),
            'budget_maximum' => $this->faker->randomFloat(2, 50000, 500000),
            'zone_recherchee' => $this->faker->city,
            'nombre_chambres' => $this->faker->numberBetween(0, 5),
            'surface_minimum' => $this->faker->randomFloat(2, 30, 300),
            'date_entree_souhaitee' => $this->faker->dateTimeBetween('+1 month', '+6 months'),
            'criteres_particuliers' => $this->faker->paragraph,
            'description' => $this->faker->paragraph,
            'statut' => $this->faker->randomElement(StatutDemandeEnum::cases()),
        ];
    }
}
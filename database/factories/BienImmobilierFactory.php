<?php

namespace Database\Factories;

use App\Models\BienImmobilier;
use App\Models\Agence;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Enums\TypeBienEnum;
use App\Enums\TypeContratEnum;

class BienImmobilierFactory extends Factory
{
    protected $model = BienImmobilier::class;

    public function definition(): array
    {
        return [
            'agence_id' => Agence::factory(),
            'titre' => $this->faker->sentence(3),
            'type_bien' => $this->faker->randomElement(TypeBienEnum::cases()),
            'type_contrat' => $this->faker->randomElement(TypeContratEnum::cases()),
            'prix' => $this->faker->randomFloat(2, 50000, 500000),
            'quartier' => $this->faker->city,
            'adresse' => $this->faker->address,
            'nombre_chambres' => $this->faker->numberBetween(0, 5),
            'nombre_salles_bain' => $this->faker->numberBetween(0, 3),
            'surface' => $this->faker->randomFloat(2, 30, 300),
            'parking_disponible' => $this->faker->boolean,
            'est_meuble' => $this->faker->boolean,
            'description' => $this->faker->paragraph,
            'statut' => $this->faker->boolean(80),
        ];
    }
}
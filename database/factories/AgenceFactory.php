<?php

namespace Database\Factories;

use App\Models\Agence;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgenceFactory extends Factory
{
    protected $model = Agence::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory()->agence(),
            'nom_agence' => $this->faker->company,
            'adresse' => $this->faker->address,
            'quartier' => $this->faker->city,
            'description' => $this->faker->paragraph,
            'logo' => null,
            'statut_validation' => $this->faker->boolean(80),
        ];
    }

    public function validee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_validation' => true,
        ]);
    }

    public function nonValidee(): static
    {
        return $this->state(fn (array $attributes) => [
            'statut_validation' => false,
        ]);
    }
}
<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use App\Enums\RoleEnum;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition(): array
    {
        return [
            'nom' => $this->faker->lastName,
            'prenom' => $this->faker->firstName,
            'email' => $this->faker->unique()->safeEmail,
            'telephone' => $this->faker->phoneNumber,
            'mot_de_passe' => Hash::make('password123'),
            'role' => RoleEnum::PARTICULIER,
            'email_verified_at' => now(),
        ];
    }

    public function particulier(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleEnum::PARTICULIER,
        ]);
    }

    public function agence(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleEnum::AGENCE,
        ]);
    }

    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => RoleEnum::ADMIN,
        ]);
    }
}
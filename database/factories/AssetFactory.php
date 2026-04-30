<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Asset>
 */
class AssetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'branch_id' => 1,
            'name' => fake()->randomElement(['Máquina Láser Diodo', 'Camilla de Masaje', 'Lámpara Lupa', 'Esterilizador', 'Sillón de Tratamiento', 'Cavitador', 'Radiofrecuencia']),
            'stock' => fake()->numberBetween(1, 5),
        ];
    }
}

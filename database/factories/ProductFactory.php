<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
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
            'name' => fake()->randomElement(['Crema Hidratante', 'Gel Limpiador', 'Protector Solar', 'Sérum Vitamina C', 'Tratamiento Capilar', 'Loción Tónica']),
            'stock' => fake()->numberBetween(0, 100),
            'price' => fake()->randomFloat(2, 20, 200),
        ];
    }
}

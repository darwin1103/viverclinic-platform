<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TreatmentOrder>
 */
class TreatmentOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => 1,
            'branch_id' => 1,
            'contracted_treatment_id' => 1,
            'total' => fake()->randomFloat(2, 50, 1000),
            'status' => fake()->randomElement(['Pagado', 'Pendiente', 'Cancelado']),
            'payment_method' => fake()->randomElement(['PSE', 'Efectivo', 'Tarjeta']),
            'currency' => 'COP',
        ];
    }
}

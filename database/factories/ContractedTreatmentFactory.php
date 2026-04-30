<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ContractedTreatment>
 */
class ContractedTreatmentFactory extends Factory
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
            'treatment_id' => 1,
            'contracted_packages' => json_encode([]),
            'contracted_additionals' => json_encode([]),
            'selected_zones' => json_encode(['Piernas completas', 'Axilas']),
            'total_price' => fake()->randomFloat(2, 50, 1500),
            'status' => fake()->randomElement(['Pending', 'Paid', 'Completed', 'Cancelled']),
            'sessions' => fake()->numberBetween(1, 10),
            'days_between_sessions' => fake()->numberBetween(7, 30),
            'terms_acepted' => true,
            'is_pregnant' => false,
        ];
    }
}

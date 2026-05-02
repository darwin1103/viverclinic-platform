<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\AccountingRecord>
 */
class AccountingRecordFactory extends Factory
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
            'user_id' => 1,
            'type' => fake()->randomElement(['income', 'expense']),
            'amount' => fake()->randomFloat(2, 10, 5000),
            'description' => fake()->sentence(),
            'reference_type' => fake()->randomElement([null, 'App\\Models\\Appointment', 'App\\Models\\ContractedTreatment']),
            'reference_id' => fake()->randomElement([null, fake()->numberBetween(1, 100)]),
        ];
    }
}

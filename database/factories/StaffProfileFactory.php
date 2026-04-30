<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StaffProfile>
 */
class StaffProfileFactory extends Factory
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
            'last_appointment_assigned' => fake()->dateTimeBetween('-1 month', 'now'),
            'commission_balance' => fake()->randomFloat(2, 0, 1000),
        ];
    }
}

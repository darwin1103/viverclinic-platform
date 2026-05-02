<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
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
            'total' => fake()->randomFloat(2, 20, 500),
            'status' => fake()->randomElement(['PENDING', 'PAID', 'DELIVERED', 'CANCELED']),
            'payment_method' => fake()->randomElement(['Cash', 'Credit Card', 'Transfer']),
            'currency' => 'COP',
            'is_juridical' => fake()->boolean(10),
            'document_type' => fake()->randomElement(['CC', 'NIT', 'CE']),
            'document_number' => fake()->numerify('##########'),
        ];
    }
}

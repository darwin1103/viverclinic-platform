<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => 1,
            'product_id' => 1,
            'product_name' => fake()->words(3, true),
            'quantity' => fake()->numberBetween(1, 5),
            'unit_price' => fake()->randomFloat(2, 10, 100),
            'subtotal' => function (array $attributes) {
                return $attributes['quantity'] * $attributes['unit_price'];
            },
        ];
    }
}

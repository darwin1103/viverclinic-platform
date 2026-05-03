<?php

namespace Database\Factories;

use App\Models\CareTip;
use Illuminate\Database\Eloquent\Factories\Factory;

class CareTipFactory extends Factory
{
    protected $model = CareTip::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(4),
            'description' => $this->faker->text(150),
            'content' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
            'image' => 'placeholder.jpg', // Placeholder for images
        ];
    }
}

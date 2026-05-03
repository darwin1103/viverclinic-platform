<?php

namespace Database\Factories;

use App\Models\Recommendation;
use Illuminate\Database\Eloquent\Factories\Factory;

class RecommendationFactory extends Factory
{
    protected $model = Recommendation::class;

    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'description' => $this->faker->text(150),
            'content' => '<p>' . implode('</p><p>', $this->faker->paragraphs(2)) . '</p>',
            'image' => 'placeholder.jpg',
        ];
    }
}

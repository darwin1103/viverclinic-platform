<?php

namespace Database\Factories;

use App\Models\Training;
use Illuminate\Database\Eloquent\Factories\Factory;

class TrainingFactory extends Factory
{
    protected $model = Training::class;

    public function definition(): array
    {
        $videos = [
            'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'https://www.youtube.com/watch?v=9bZkp7q19f0',
            'https://www.youtube.com/watch?v=L_jWHffIx5E'
        ];

        return [
            'title' => $this->faker->words(3, true),
            'description' => $this->faker->text(150),
            'content' => '<p>' . implode('</p><p>', $this->faker->paragraphs(3)) . '</p>',
            'youtube_url' => $this->faker->randomElement($videos),
        ];
    }
}

<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Appointment>
 */
class AppointmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'contracted_treatment_id' => 1,
            'schedule' => fake()->dateTimeBetween('-1 month', '+2 months'),
            'status' => fake()->randomElement(['Pendiente', 'Completado', 'No asistió', 'Cancelada']),
            'session_number' => fake()->numberBetween(1, 5),
            'staff_user_id' => null, // Typically assigned later or randomly
            'attended' => fake()->boolean(80), // 80% chance of being attended if past
            'notification_reminder_sent_48' => fake()->boolean(),
            'notification_reminder_sent_36' => fake()->boolean(),
            'notification_reminder_sent_26' => fake()->boolean(),
            'review' => fake()->optional(0.3)->sentence(), // 30% chance of having a review
            'review_score' => fake()->optional(0.3)->numberBetween(1, 5),
            'uses_of_hair_removal_shots' => fake()->optional(0.5)->numberBetween(100, 500),
        ];
    }
}

<?php

namespace Database\Seeders;

use App\Models\CareTip;
use App\Models\Recommendation;
use App\Models\Training;
use App\Models\Branch;
use Illuminate\Database\Seeder;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        $branches = Branch::all();

        if ($branches->isEmpty()) {
            $this->command->info('No branches found. Please seed branches first.');
            return;
        }

        foreach ($branches as $branch) {
            // Create 3 care tips per branch
            CareTip::factory()->count(3)->create([
                'branch_id' => $branch->id
            ]);

            // Create 3 recommendations per branch
            Recommendation::factory()->count(3)->create([
                'branch_id' => $branch->id
            ]);
        }

        // Trainings are global, create 5
        Training::factory()->count(5)->create();

        $this->command->info('Test content (Tips, Recommendations, Trainings) seeded successfully.');
    }
}

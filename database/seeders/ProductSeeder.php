<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branchId = \App\Models\Branch::first()->id ?? 1;

        Product::firstOrCreate(
            ['name' => 'Crema Hidratante Facial'],
            [
                'branch_id' => $branchId,
                'price' => 50000,
                'stock' => 100,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Protector Solar SPF 50+'],
            [
                'branch_id' => $branchId,
                'price' => 85000,
                'stock' => 50,
            ]
        );

        Product::firstOrCreate(
            ['name' => 'Gel Despigmentante'],
            [
                'branch_id' => $branchId,
                'price' => 120000,
                'stock' => 30,
            ]
        );
    }
}

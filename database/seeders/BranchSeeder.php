<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Branch::create([
            'name' => 'Sucursal01',
            'address' => 'Sucursal01',
            'phone' => 'Sucursal01',
        ]);

        Branch::create([
            'name' => 'Sucursal02',
            'address' => 'Sucursal02',
            'phone' => 'Sucursal02',
        ]);

        Branch::create([
            'name' => 'Sucursal03',
            'address' => 'Sucursal03',
            'phone' => 'Sucursal03',
        ]);

        Branch::create([
            'name' => 'Sucursal03',
            'address' => 'Sucursal03',
            'phone' => 'Sucursal03',
        ]);

    }

}

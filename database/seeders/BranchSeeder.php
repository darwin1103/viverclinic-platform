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
            'address' => 'Direccion sucursal01 av. 123',
            'phone' => '12345678901',
            'google_maps_url' => 'https://www.google.com/maps/place/Madrid,+Spain/@40.4208713,-3.724774,13z',
        ]);

        Branch::create([
            'name' => 'Sucursal02',
            'address' => 'Direccion sucursal02 av. 123',
            'phone' => '12345678902',
            'google_maps_url' => 'https://www.google.com/maps/place/Madrid,+Spain/@40.4208713,-3.724774,13z',
        ]);

        Branch::create([
            'name' => 'Sucursal03',
            'address' => 'Direccion sucursal03 av. 123',
            'phone' => '12345678903',
            'google_maps_url' => 'https://www.google.com/maps/place/Madrid,+Spain/@40.4208713,-3.724774,13z',
        ]);

    }

}

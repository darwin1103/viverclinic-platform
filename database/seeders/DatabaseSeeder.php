<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        Role::firstOrCreate(['name' => 'SUPER_ADMIN']);
        $adminRole = Role::firstOrCreate(['name' => 'ADMIN']);
        $employeeRole = Role::firstOrCreate(['name' => 'EMPLOYEE']);
        $patientRole = Role::firstOrCreate(['name' => 'PATIENT']);

        $user = User::create([
            'name' => 'Super Admin',
            'email' => 'super_admin@viverclinic.com',
            'password' => '$2y$12$e1hOplDQvYp3qsA1Bf/JV.iaFaUXJMieRuxyJm/iwSq/siWiMtA5W'
        ]);

        $user->assignRole('SUPER_ADMIN');
        
        Permission::firstOrCreate(['name' => 'EMPLOYEE_DASHBOARD']);
        Permission::firstOrCreate(['name' => 'PATIENT_DASHBOARD']);

        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'ADMIN_DASHBOARD']));

        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'EMPLOYEE_DASHBOARD']));

        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'PATIENT_DASHBOARD']));

        Genre::create([
            'name' => 'Masculine',
            'code' => 'M',
            'status' => '1'
        ]);

        Genre::create([
            'name' => 'Female',
            'code' => 'F',
            'status' => '1'
        ]);
    }
}

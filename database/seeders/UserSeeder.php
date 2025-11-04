<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $adminUser1 = User::create([
            'name' => 'ViverClinic Admin',
            'email' => 'viverclinicadmin@viverclinic.com',
            'password' => Hash::make('Yt7C5sj91c51hAQbYMQM'),
        ]);

        $adminUser1->assignRole('SUPER_ADMIN');

        $adminUser2 = User::create([
            'name' => 'Xavier',
            'email' => '1@1.com',
            'password' => Hash::make('1'),
        ]);

        $adminUser2->assignRole('SUPER_ADMIN');

        $client01 = User::create([
            'name' => 'cliente01',
            'email' => 'c01@1.com',
            'password' => Hash::make('1'),
            'birthday' => '2025-10-29',
            'gender_id' => 1,
            'informed_consent' => 0,
            'citizenship' => 'test',
            'document_type_id' => 1,
            'document_number' => 'test',
            'profession' => 'test',
            'phone' => '123456798',
            'address' => 'test',
            'surgery' => 'test',
            'recommendation' => 'test',
            'terms_conditions' => 1,
            'directory' => null,
            'photo_profile' => null,
            'not_pregnant' => 1,
            'pathological_id' => 1,
            'toxicological_id' => 1,
            'gyneco_obstetric_id' => 1,
            'medication_id' => 1,
            'dietary_id' => 1,
            'treatment_id' => 1,
        ]);

        $client01->assignRole('PATIENT');

        $client01->patientProfile()->create([
            'branch_id' => 1,
        ]);

        $client02 = User::create([
            'name' => 'cliente02',
            'email' => 'c02@1.com',
            'password' => Hash::make('1'),
            'birthday' => '2025-10-29',
            'gender_id' => 2,
            'informed_consent' => 0,
            'citizenship' => 'test',
            'document_type_id' => 2,
            'document_number' => 'test',
            'profession' => 'test',
            'phone' => '223456798',
            'address' => 'test',
            'surgery' => 'test',
            'recommendation' => 'test',
            'terms_conditions' => 2,
            'directory' => null,
            'photo_profile' => null,
            'not_pregnant' => 2,
            'pathological_id' => 2,
            'toxicological_id' => 2,
            'gyneco_obstetric_id' => 2,
            'medication_id' => 2,
            'dietary_id' => 2,
            'treatment_id' => 2,
        ]);

        $client02->assignRole('PATIENT');

        $client02->patientProfile()->create([
            'branch_id' => 2,
        ]);

        $staff01 = User::create([
            'name' => 'staff01',
            'email' => 's01@1.com',
            'password' => Hash::make('1'),
        ]);

        $staff01->assignRole('EMPLOYEE');
        $staff01->staffProfile()->create([
            'branch_id' => 1,
        ]);

        $staff02 = User::create([
            'name' => 'staff02',
            'email' => 's02@1.com',
            'password' => Hash::make('1'),
        ]);

        $staff02->assignRole('EMPLOYEE');
        $staff02->staffProfile()->create([
            'branch_id' => 2,
        ]);

        $owner01 = User::create([
            'name' => 'owner01',
            'email' => 'o01@1.com',
            'password' => Hash::make('1'),
        ]);

        $owner01->assignRole('OWNER');
        $owner01->ownerProfile()->create([
            'branch_id' => 1,
        ]);

        $owner02 = User::create([
            'name' => 'owner02',
            'email' => 'o02@1.com',
            'password' => Hash::make('1'),
        ]);

        $owner02->assignRole('OWNER');
        $owner02->ownerProfile()->create([
            'branch_id' => 2,
        ]);

    }

}

<?php

namespace Database\Seeders;

use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\Permission;
use App\Models\Role;
use App\Models\ToxicologicalCondition;
use App\Models\TreatmentCondition;
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

        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'ADMIN_DASHBOARD']));
        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'ADMIN_DASHBOARD_ROLE_MANAGEMENT']));
        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'ADMIN_DASHBOARD_USER_MANAGEMENT']));
        $adminRole->givePermissionTo(Permission::firstOrCreate(['name' => 'ADMIN_DASHBOARD_BRANCH_MANAGEMENT']));

        $employeeRole->givePermissionTo(Permission::firstOrCreate(['name' => 'EMPLOYEE_DASHBOARD']));

        $patientRole->givePermissionTo(Permission::firstOrCreate(['name' => 'PATIENT_DASHBOARD']));

        Gender::create(['name' => 'Masculine','code' => 'M','status' => '1']);
        Gender::create(['name' => 'Female','code' => 'F','status' => '1']);

        DocumentType::create(['name' => 'Citizenship Card','status' => '1']);
        DocumentType::create(['name' => 'Foreigners Identity Card','status' => '1']);
        DocumentType::create(['name' => 'Passport','status' => '1']);
        DocumentType::create(['name' => 'Identity card','status' => '1']);

        PathologicalCondition::create(['name' => 'Cholesterol','status' => '1']);
        PathologicalCondition::create(['name' => 'Autoimmune Disease','status' => '1']);
        PathologicalCondition::create(['name' => 'You suffer from heart disease','status' => '1']);
        PathologicalCondition::create(['name' => 'Allergies','status' => '1']);
        PathologicalCondition::create(['name' => 'High Blood Pressure','status' => '1']);
        PathologicalCondition::create(['name' => 'Diabetes','status' => '1']);
        PathologicalCondition::create(['name' => 'Varicose veins','status' => '1']);
        PathologicalCondition::create(['name' => 'Migraines','status' => '1']);
        PathologicalCondition::create(['name' => 'Thyroid','status' => '1']);
        PathologicalCondition::create(['name' => 'Gastrointestinal problems','status' => '1']);
        PathologicalCondition::create(['name' => 'Joint Pain','status' => '1']);
        PathologicalCondition::create(['name' => 'Fluid retention','status' => '1']);
        PathologicalCondition::create(['name' => 'None of the above','status' => '1']);
        PathologicalCondition::create(['name' => '2 or more of the above','status' => '1']);

        ToxicologicalCondition::create(['name' => 'You consume liquor','status' => '1']);
        ToxicologicalCondition::create(['name' => 'You use drugs','status' => '1']);
        ToxicologicalCondition::create(['name' => 'You smoke','status' => '1']);
        ToxicologicalCondition::create(['name' => 'None of the above','status' => '1']);
        ToxicologicalCondition::create(['name' => '2 or more of the above','status' => '1']);

        GynecoObstetricCondition::create(['name' => 'Polycystic ovaries', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Endometriosis', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Uterine fibroids', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Endocrine disorders', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Sexually transmitted infections', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Urinary tract infections', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Pelvic inflammatory disease', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Inguinal hernias', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Epididymitis', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Hydrocele', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Varicocele', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Testicular injuries', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'Inflammation of the penis', 'status' => 1]);
        GynecoObstetricCondition::create(['name' => 'None of the above', 'status' => 1]);

        MedicationCondition::create(['name' => 'Analgesics and anti-inflammatories, such as Ibuprofen and Naproxen', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antacids, such as Omeprazole or Ranitidine', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antibiotics, such as Ciprofloxacin, Azithromycin, or Gentamicin', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antihistamines, such as Ebastine, Loratadine or Claritin', 'status' => 1]);
        MedicationCondition::create(['name' => 'Antidepressants, such as Amitriptyline, Bromazepam, or Fluoxetine', 'status' => 1]);
        MedicationCondition::create(['name' => 'Diuretics, such as Cyclothiazide or Quinetazone', 'status' => 1]);
        MedicationCondition::create(['name' => 'Corticosteroids, such as Prednisone or Hydrocortisone', 'status' => 1]);
        MedicationCondition::create(['name' => 'To lower cholesterol, such as Lovastatin or Atorvastatin', 'status' => 1]);
        MedicationCondition::create(['name' => 'To combat acne, such as Roaccutane (Isotretinoin)', 'status' => 1]);
        MedicationCondition::create(['name' => 'None of the above', 'status' => 1]);
        MedicationCondition::create(['name' => '2 or more of the above', 'status' => 1]);

        DietaryCondition::create(['name' => 'You consume foods high in sugar', 'status' => 1]);
        DietaryCondition::create(['name' => 'You consume ultra-processed foods', 'status' => 1]);
        DietaryCondition::create(['name' => 'You have a special diet', 'status' => 1]);
        DietaryCondition::create(['name' => 'You do intermittent fasting', 'status' => 1]);
        DietaryCondition::create(['name' => 'You consume dairy products', 'status' => 1]);
        DietaryCondition::create(['name' => '2 more options apply to me', 'status' => 1]);
        DietaryCondition::create(['name' => 'None of the above', 'status' => 1]);

        TreatmentCondition::create(['name' => 'Laser hair removal', 'status' => 1]);
        TreatmentCondition::create(['name' => 'Reduction', 'status' => 1]);
    }
}

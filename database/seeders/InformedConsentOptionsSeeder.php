<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\ToxicologicalCondition;

class InformedConsentOptionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Gender::firstOrCreate(['code' => 'M'], ['name' => 'Masculine','status' => '1']);
        Gender::firstOrCreate(['code' => 'F'], ['name' => 'Female','status' => '1']);

        DocumentType::firstOrCreate(['name' => 'Citizenship Card'], ['status' => '1']);
        DocumentType::firstOrCreate(['name' => 'Foreigners Identity Card'], ['status' => '1']);
        DocumentType::firstOrCreate(['name' => 'Passport'], ['status' => '1']);
        DocumentType::firstOrCreate(['name' => 'Identity card'], ['status' => '1']);

        PathologicalCondition::firstOrCreate(['name' => 'Cholesterol'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Autoimmune Disease'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'You suffer from heart disease'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Allergies'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'High Blood Pressure'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Diabetes'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Varicose veins'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Migraines'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Thyroid'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Gastrointestinal problems'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Joint Pain'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'Fluid retention'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => 'None of the above'], ['status' => '1']);
        PathologicalCondition::firstOrCreate(['name' => '2 or more of the above'], ['status' => '1']);

        ToxicologicalCondition::firstOrCreate(['name' => 'You consume liquor'], ['status' => '1']);
        ToxicologicalCondition::firstOrCreate(['name' => 'You use drugs'], ['status' => '1']);
        ToxicologicalCondition::firstOrCreate(['name' => 'You smoke'], ['status' => '1']);
        ToxicologicalCondition::firstOrCreate(['name' => 'None of the above'], ['status' => '1']);
        ToxicologicalCondition::firstOrCreate(['name' => '2 or more of the above'], ['status' => '1']);

        GynecoObstetricCondition::firstOrCreate(['name' => 'Polycystic ovaries'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Endometriosis'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Uterine fibroids'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Endocrine disorders'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Sexually transmitted infections'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Urinary tract infections'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Pelvic inflammatory disease'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Inguinal hernias'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Epididymitis'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Hydrocele'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Varicocele'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Testicular injuries'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'Inflammation of the penis'], ['status' => 1]);
        GynecoObstetricCondition::firstOrCreate(['name' => 'None of the above'], ['status' => 1]);

        MedicationCondition::firstOrCreate(['name' => 'Analgesics and anti-inflammatories, such as Ibuprofen and Naproxen'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'Antacids, such as Omeprazole or Ranitidine'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'Antibiotics, such as Ciprofloxacin, Azithromycin, or Gentamicin'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'Antihistamines, such as Ebastine, Loratadine or Claritin'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'Antidepressants, such as Amitriptyline, Bromazepam, or Fluoxetine'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'Diuretics, such as Cyclothiazide or Quinetazone'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'Corticosteroids, such as Prednisone or Hydrocortisone'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'To lower cholesterol, such as Lovastatin or Atorvastatin'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'To combat acne, such as Roaccutane (Isotretinoin)'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => 'None of the above'], ['status' => 1]);
        MedicationCondition::firstOrCreate(['name' => '2 or more of the above'], ['status' => 1]);

        DietaryCondition::firstOrCreate(['name' => 'You consume foods high in sugar'], ['status' => 1]);
        DietaryCondition::firstOrCreate(['name' => 'You consume ultra-processed foods'], ['status' => 1]);
        DietaryCondition::firstOrCreate(['name' => 'You have a special diet'], ['status' => 1]);
        DietaryCondition::firstOrCreate(['name' => 'You do intermittent fasting'], ['status' => 1]);
        DietaryCondition::firstOrCreate(['name' => 'You consume dairy products'], ['status' => 1]);
        DietaryCondition::firstOrCreate(['name' => '2 more options apply to me'], ['status' => 1]);
        DietaryCondition::firstOrCreate(['name' => 'None of the above'], ['status' => 1]);

    }

}

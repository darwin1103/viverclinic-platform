<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Branch;
use App\Models\Treatment;
use App\Models\ContractedTreatment;
use App\Models\Appointment;
use App\Models\TreatmentOrder;
use Carbon\Carbon;
use Spatie\Permission\Models\Role;

// Create a branch if none exists
$branch = Branch::first();
if (!$branch) {
    $branch = Branch::create(['name' => 'Sucursal Norte', 'address' => 'Calle Falsa 123', 'phone' => '123456789']);
}

// Ensure role PATIENT exists
if (!Role::where('name', 'PATIENT')->exists()) {
    Role::create(['name' => 'PATIENT']);
}

$names = ['Valeria Torres', 'Andrés Mendoza', 'Camila Ríos', 'Julián Ortiz', 'Sofía Castillo'];
$treatments = ['Depilación Láser Piernas', 'Rejuvenecimiento Facial', 'Reducción Abdomen', 'Limpieza Profunda', 'Tratamiento Acné'];

// Get or create treatments
$tModels = [];
foreach ($treatments as $t) {
    $tModel = Treatment::firstOrCreate(['name' => $t], ['description' => 'Tratamiento de alta calidad', 'active' => 1, 'sessions' => 10, 'days_between_sessions' => 30]);
    $tModels[] = $tModel;
}

// Create 5 patients created in the last 7 days
foreach ($names as $idx => $name) {
    $user = User::where('email', 'patient'.$idx.'@viverclinic.com')->first();
    if (!$user) {
        $user = User::create([
            'name' => $name,
            'email' => 'patient'.$idx.'@viverclinic.com',
            'password' => bcrypt('password123'),
            'created_at' => Carbon::now()->subDays(rand(1, 6)),
        ]);
        $user->assignRole('PATIENT');
    }
    
    // Contract a treatment
    $ct = ContractedTreatment::create([
        'user_id' => $user->id,
        'branch_id' => $branch->id,
        'treatment_id' => $tModels[$idx]->id,
        'total_price' => rand(500, 2000) * 1000,
        'status' => 'Pending',
        'sessions' => 10,
        'days_between_sessions' => 30,
        'selected_zones' => '[]',
        'contracted_packages' => '[]',
        'contracted_additionals' => '[]',
        'terms_acepted' => 1,
        'is_pregnant' => 0,
    ]);

    // Create a payment
    TreatmentOrder::create([
        'user_id' => $user->id,
        'branch_id' => $branch->id,
        'contracted_treatment_id' => $ct->id,
        'total' => $ct->total_price,
        'status' => 'Pagado',
        'payment_method' => ['Tarjeta', 'PSE', 'Efectivo'][rand(0,2)],
        'created_at' => Carbon::now()->subHours(rand(1, 48)),
    ]);

    // Create an appointment for TODAY
    Appointment::create([
        'contracted_treatment_id' => $ct->id,
        'schedule' => Carbon::now()->setHour(rand(9, 17))->setMinute(rand(0, 59)),
        'status' => 'Scheduled',
        'session_number' => 1,
    ]);

    // Create an appointment over the last 7 days for the chart
    Appointment::create([
        'contracted_treatment_id' => $ct->id,
        'schedule' => Carbon::now()->subDays(rand(1, 6))->setHour(rand(9, 17)),
        'status' => 'Attended',
        'session_number' => 1,
    ]);
}

// Create more appointments to populate the chart fully
for($i=0; $i<15; $i++) {
    Appointment::create([
        'contracted_treatment_id' => ContractedTreatment::inRandomOrder()->first()->id,
        'schedule' => Carbon::now()->subDays(rand(1, 6))->setHour(rand(9, 17)),
        'status' => 'Attended',
        'session_number' => rand(1,10),
    ]);
}

echo "Demo data created successfully!";

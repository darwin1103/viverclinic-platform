<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class MockDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $branches = \App\Models\Branch::all();
        $treatments = \App\Models\Treatment::all();

        if ($branches->isEmpty() || $treatments->isEmpty()) {
            $this->command->warn('Asegúrate de haber ejecutado los seeders principales (BranchSeeder, TreatmentSeeder) primero.');
            return;
        }

        $this->command->info('Generando Empleados...');
        $employees = \App\Models\User::factory(10)->create()->each(function ($user) use ($branches) {
            $user->assignRole('EMPLOYEE');
            \App\Models\StaffProfile::factory()->create([
                'user_id' => $user->id,
                'branch_id' => $branches->random()->id,
            ]);
            $user->employeesBranches()->attach($branches->random()->id);
        });

        $this->command->info('Generando Pacientes, Tratamientos Contratados, Órdenes y Citas...');
        \App\Models\User::factory(50)->create()->each(function ($user) use ($branches, $treatments, $employees) {
            $user->assignRole('PATIENT');
            
            $branch = $branches->random();
            
            \App\Models\PatientProfile::factory()->create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
            ]);
            $user->patientsBranches()->attach($branch->id);

            // Create 1-3 Contracted Treatments for each patient
            $numTreatments = rand(1, 3);
            for ($i = 0; $i < $numTreatments; $i++) {
                $treatment = $treatments->random();
                $contractedTreatment = \App\Models\ContractedTreatment::factory()->create([
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'treatment_id' => $treatment->id,
                ]);

                // Create a TreatmentOrder to record the payment
                // Force some to be today to show up in "Ingreso diario"
                $orderDate = rand(1, 10) > 8 ? Carbon::today() : fake()->dateTimeBetween('-1 month', 'now');
                \App\Models\TreatmentOrder::factory()->create([
                    'user_id' => $user->id,
                    'branch_id' => $branch->id,
                    'contracted_treatment_id' => $contractedTreatment->id,
                    'status' => fake()->randomElement(['Pagado', 'Pendiente', 'Pagado', 'Cancelado']),
                    'created_at' => $orderDate,
                ]);

                // Create Appointments for this contracted treatment
                $numAppointments = rand(2, 5);
                for ($j = 0; $j < $numAppointments; $j++) {
                    // Force some appointments to be today to show in "Citas de hoy"
                    $apptDate = rand(1, 10) > 7 ? Carbon::today()->addHours(rand(8, 18)) : fake()->dateTimeBetween('-1 month', '+1 month');
                    \App\Models\Appointment::factory()->create([
                        'contracted_treatment_id' => $contractedTreatment->id,
                        'staff_user_id' => $employees->random()->id,
                        'session_number' => $j + 1,
                        'schedule' => $apptDate,
                    ]);
                }
            }
        });

        $this->command->info('Generando Registros Contables...');
        \App\Models\User::factory(5)->create()->each(function ($adminUser) use ($branches) {
            $adminUser->assignRole('ADMIN');
            for ($i = 0; $i < 15; $i++) {
                // Force some to be today and 'expense' to show up in "Egresos de hoy"
                $recordDate = rand(1, 10) > 7 ? Carbon::today() : fake()->dateTimeBetween('-1 month', 'now');
                $type = rand(1, 10) > 5 ? 'expense' : 'income';
                \App\Models\AccountingRecord::factory()->create([
                    'branch_id' => $branches->random()->id,
                    'user_id' => $adminUser->id,
                    'created_at' => $recordDate,
                    'type' => $type,
                ]);
            }
        });

        $this->command->info('Generando Propietarios...');
        \App\Models\User::factory(2)->create()->each(function ($user) {
            $user->assignRole('OWNER');
            \App\Models\OwnerProfile::factory()->create(['user_id' => $user->id]);
        });

        $this->command->info('Generando Activos (Assets)...');
        for ($i = 0; $i < 10; $i++) {
            \App\Models\Asset::factory()->create([
                'branch_id' => $branches->random()->id
            ]);
        }

        $this->command->info('Generando Productos...');
        $products = collect();
        for ($i = 0; $i < 15; $i++) {
            $products->push(\App\Models\Product::factory()->create([
                'branch_id' => $branches->random()->id
            ]));
        }

        $this->command->info('Generando Órdenes de Compra...');
        $patients = \App\Models\User::role('PATIENT')->get();
        if ($patients->isNotEmpty() && $products->isNotEmpty()) {
            for ($i = 0; $i < 20; $i++) {
                $order = \App\Models\Order::factory()->create([
                    'user_id' => $patients->random()->id,
                    'branch_id' => $branches->random()->id,
                ]);

                $numItems = rand(1, 3);
                $total = 0;
                for ($j = 0; $j < $numItems; $j++) {
                    $product = $products->random();
                    $quantity = rand(1, 3);
                    $item = \App\Models\OrderItem::factory()->create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'product_name' => $product->name,
                        'unit_price' => $product->price,
                        'quantity' => $quantity,
                        'subtotal' => $product->price * $quantity,
                    ]);
                    $total += $item->subtotal;
                }
                
                $order->update(['total' => $total]);
            }
        }

        $this->command->info('MockDataSeeder ejecutado con éxito.');
    }
}

<?php
use App\Models\Branch;
use App\Models\User;
use App\Models\Treatment;
use App\Models\ContractedTreatment;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

$branchA = Branch::where('name', 'Branch A')->first();
$branchB = Branch::where('name', 'Branch B')->first();

$adminA = User::where('email', 'adminA@example.com')->first();
$patientA = User::firstOrCreate(['email' => 'patientA@example.com'], ['name' => 'Patient A', 'password' => bcrypt('password')]);
$patientB = User::where('email', 'patientB@example.com')->first();

$treatment = Treatment::firstOrCreate(['name' => 'Laser', 'active' => true], ['description' => 'Laser treatment', 'sessions' => 5, 'days_between_sessions' => 30]);

$ctA = ContractedTreatment::firstOrCreate([
    'user_id' => $patientA->id,
    'branch_id' => $branchA->id,
    'treatment_id' => $treatment->id,
], [
    'total_price' => 100,
    'status' => 'active',
    'sessions' => 5,
    'days_between_sessions' => 30,
    'terms_acepted' => true,
    'is_pregnant' => false,
    'selected_zones' => '[]',
]);

$ctB = ContractedTreatment::firstOrCreate([
    'user_id' => $patientB->id,
    'branch_id' => $branchB->id,
    'treatment_id' => $treatment->id,
], [
    'total_price' => 100,
    'status' => 'active',
    'sessions' => 5,
    'days_between_sessions' => 30,
    'terms_acepted' => true,
    'is_pregnant' => false,
    'selected_zones' => '[]',
]);

$appointmentA = Appointment::firstOrCreate([
    'contracted_treatment_id' => $ctA->id,
], [
    'schedule' => now()->addDays(1),
    'status' => 'Pendiente',
    'session_number' => 1
]);

$appointmentB = Appointment::firstOrCreate([
    'contracted_treatment_id' => $ctB->id,
], [
    'schedule' => now()->addDays(1),
    'status' => 'Pendiente',
    'session_number' => 1
]);

echo "Setup done. Appt A: {$appointmentA->id}, Appt B: {$appointmentB->id}\n";

// --- Test Visibility ---
Auth::login($adminA);

$requestFetch = Request::create('/admin/appointments/fetch', 'POST', [
    'start_date' => now()->subDays(1)->format('Y-m-d'),
    'end_date' => now()->addDays(5)->format('Y-m-d')
]);
$requestFetch->headers->set('Accept', 'application/json');

$responseFetch = app()->handle($requestFetch);
$data = json_decode($responseFetch->getContent(), true);

$visibleIds = array_column($data['appointments'] ?? [], 'id');
echo "Visible Appointments: " . implode(', ', $visibleIds) . "\n";
if (in_array($appointmentB->id, $visibleIds)) {
    echo "FAIL 1\n";
} else {
    echo "PASS 1\n";
}

// --- Test Access ---
app()->instance(\App\Http\Middleware\VerifyCsrfToken::class, new class {
    public function handle($request, $next) { return $next($request); }
});

$requestAccess = Request::create("/admin/appointments/{$appointmentB->id}/mark-attended", 'POST', ['attended' => true]);
$requestAccess->headers->set('Accept', 'application/json');
$responseAccess = app()->handle($requestAccess);

echo "Status Code: " . $responseAccess->getStatusCode() . "\n";
if ($responseAccess->getStatusCode() == 403 || $responseAccess->getStatusCode() == 404) {
    echo "PASS 2\n";
} else {
    echo "FAIL 2\n";
}

<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$referrer = App\Models\User::where('email', 'c01@1.com')->first();
echo "Referrer found: " . ($referrer ? 'Yes' : 'No') . "\n";
if (!$referrer) exit;

// Create dummy request
$request = Illuminate\Http\Request::create('/register', 'POST', [
    'name' => 'QA Round3',
    'email' => 'qar3@test.com',
    'password' => '12345678',
    'password_confirmation' => '12345678',
    'branchId' => 1,
    'ref' => $referrer->referral_code
]);

$response = app()->handle($request);
echo "Registration status: " . $response->getStatusCode() . "\n";

$newUser = App\Models\User::where('email', 'qar3@test.com')->first();
echo "New user created: " . ($newUser ? 'Yes' : 'No') . "\n";

if ($newUser) {
    echo "Associated with referrer: " . ($newUser->referred_by_id === $referrer->id ? 'Yes' : 'No') . "\n";
    $referral = App\Models\Referral::where('referred_email', 'qar3@test.com')->first();
    echo "Referral record created: " . ($referral ? 'Yes' : 'No') . "\n";
}

// Check sessions
$sessions = App\Models\PatientProfile::where('user_id', $referrer->id)->first()->free_sessions ?? 0;
echo "Referrer free sessions: " . $sessions . "\n";

// Check commissions (just looking for any commission related to this referral or recent)
// Assuming there is a Commission model
$commissions = class_exists('App\Models\Commission') ? App\Models\Commission::count() : 'No model';
echo "Commissions exists: " . $commissions . "\n";


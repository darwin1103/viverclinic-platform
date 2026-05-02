<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$referrer = App\Models\User::where('email', 'c01@1.com')->first();
request()->merge(['ref' => $referrer->referral_code]);

$controller = new App\Http\Controllers\Auth\RegisterController();
$reflection = new ReflectionClass($controller);
$method = $reflection->getMethod('create');
$method->setAccessible(true);

try {
    $user = $method->invoke($controller, [
        'name' => 'QA Round3',
        'email' => 'qar3@test.com',
        'password' => '12345678',
        'password_confirmation' => '12345678',
        'branchId' => 1
    ]);
    echo "User created: " . $user->id . "\n";
    echo "Associated: " . ($user->referred_by_id === $referrer->id ? 'Yes' : 'No') . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

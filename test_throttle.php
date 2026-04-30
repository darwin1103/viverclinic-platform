<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

for ($i = 0; $i < 10; $i++) {
    $request = Illuminate\Http\Request::create('/login', 'POST', ['email' => 'fake@example.com', 'password' => 'wrong']);
    // Bypass CSRF by removing the middleware or simulating only the throttle middleware
    // We can just call the RateLimiter directly to see if it works
    $executed = Illuminate\Support\Facades\RateLimiter::attempt(
        'login:'.('fake@example.com'),
        5,
        function() { return true; }
    );
    echo "Attempt " . ($i+1) . " allowed? " . ($executed ? 'Yes' : 'No') . "\n";
}

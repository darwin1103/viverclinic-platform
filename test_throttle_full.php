<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

for ($i = 0; $i < 7; $i++) {
    $request = Illuminate\Http\Request::create('/login', 'POST', ['email' => 'fake2@example.com', 'password' => 'wrong']);
    // Disable VerifyCsrfToken for this test by replacing it with a dummy closure or removing from middleware
    $app->instance(\App\Http\Middleware\VerifyCsrfToken::class, new class {
        public function handle($request, $next) { return $next($request); }
    });
    
    $response = app()->handle($request);
    echo "Attempt " . ($i+1) . " status: " . $response->getStatusCode() . "\n";
}

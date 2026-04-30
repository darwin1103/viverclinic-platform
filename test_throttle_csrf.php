<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

session()->start();
$token = csrf_token();

for ($i = 0; $i < 7; $i++) {
    $request = Illuminate\Http\Request::create('/login', 'POST', [
        'email' => 'fake@example.com', 
        'password' => 'wrong',
        '_token' => $token
    ]);
    $request->setLaravelSession(session());
    $response = app()->handle($request);
    echo "Attempt " . ($i+1) . " status: " . $response->getStatusCode() . "\n";
}

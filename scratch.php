<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = ['users', 'treatments', 'contracted_treatments', 'appointments', 'treatment_orders'];
foreach($tables as $t) {
    echo "Table $t:\n";
    $columns = \Illuminate\Support\Facades\Schema::getColumnListing($t);
    print_r($columns);
}

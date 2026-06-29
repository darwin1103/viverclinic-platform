<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::role('ADMIN')->with(['adminsBranches', 'adminProfile'])->get();
foreach($users as $u) {
    echo $u->id . ' - Branches: ' . $u->adminsBranches->count() . ' - ProfileBranch: ' . ($u->adminProfile->branch_id ?? 'null') . PHP_EOL;
}

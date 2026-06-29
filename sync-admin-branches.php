<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$users = App\Models\User::role('ADMIN')->with(['adminsBranches', 'adminProfile'])->get();
foreach($users as $u) {
    if ($u->adminsBranches->count() === 0 && $u->adminProfile && $u->adminProfile->branch_id) {
        $u->adminsBranches()->sync([$u->adminProfile->branch_id]);
        echo "Synced branch " . $u->adminProfile->branch_id . " for user " . $u->id . PHP_EOL;
    }
}
echo "Done" . PHP_EOL;

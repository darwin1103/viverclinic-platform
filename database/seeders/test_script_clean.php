use App\Models\Branch;
use App\Models\User;
use App\Models\Asset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

$branchA = Branch::firstOrCreate(['name' => 'Branch A'], ['slug' => 'branch-a', 'address' => '123 A St']);
$branchB = Branch::firstOrCreate(['name' => 'Branch B'], ['slug' => 'branch-b', 'address' => '456 B St']);

$adminUser = User::firstOrCreate(
    ['email' => 'adminA@example.com'],
    ['name' => 'Admin A', 'password' => Hash::make('password')]
);
$adminUser->assignRole('ADMIN');
if (!$adminUser->adminsBranches->contains($branchA->id)) {
    $adminUser->adminsBranches()->attach($branchA->id);
}

$patientB = User::firstOrCreate(
    ['email' => 'patientB@example.com'],
    ['name' => 'Patient B', 'password' => Hash::make('password')]
);
$patientB->assignRole('PATIENT');
if (!$patientB->patientsBranches->contains($branchB->id)) {
    $patientB->patientsBranches()->attach($branchB->id);
}

$assetB = Asset::firstOrCreate(
    ['name' => 'Asset B', 'branch_id' => $branchB->id],
    ['stock' => 10]
);

$patientRef = User::firstOrCreate(
    ['email' => 'patientRef@example.com'],
    ['name' => 'Patient Ref', 'password' => Hash::make('password')]
);
$patientRef->assignRole('PATIENT');
// The booted method will generate a referral_code automatically.

echo json_encode([
    'adminA' => $adminUser->email,
    'patientB_id' => $patientB->id,
    'assetB_id' => $assetB->id,
    'patientRef_code' => $patientRef->referral_code
]);

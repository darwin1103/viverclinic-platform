<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Referral;
use App\Models\AccountingRecord;
use App\Models\ContractedTreatment;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;

echo "=== INICIANDO PRUEBA DE REFERIDOS EN CONSOLA ===\n";

// 1. Encontrar al cliente cd1@mail.com
$referrer = User::where('email', 'cd1@mail.com')->first();
if (!$referrer) {
    echo "Error: No se encontró al cliente cd1@mail.com\n";
    exit(1);
}

echo "Referidor encontrado: {$referrer->name} (Email: {$referrer->email}, Codigo: {$referrer->referral_code})\n";

// 2. Revisar si el referidor tiene ContractedTreatment
$latestContractBefore = ContractedTreatment::where('user_id', $referrer->id)->latest()->first();
$sessionsBefore = $latestContractBefore ? $latestContractBefore->sessions : 'Ninguno';
echo "Sesiones del referidor antes: {$sessionsBefore}\n";

// 3. Simular registro
$request = Request::create('/register', 'POST', [
    'name' => 'Prueba Consola Referido',
    'email' => 'prueba.consola' . time() . '@mail.com',
    'password' => 'password123',
    'password_confirmation' => 'password123',
    'branchId' => 1,
    'ref' => $referrer->referral_code
]);

// Ejecutar controlador de registro
$controller = new RegisterController();
try {
    $response = $controller->register($request);
    echo "Registro ejecutado.\n";
} catch (\Exception $e) {
    if (strpos($e->getMessage(), 'Too Many Requests') !== false) {
        echo "Error: Rate limiting activo (Too Many Requests).\n";
        // Intentaremos crearlo directamente para saltar el rate limit si es necesario
    } else {
        echo "Excepción durante registro: " . $e->getMessage() . "\n";
    }
}

// Verificar directamente
$referred = User::where('email', $request->email)->first();
if ($referred) {
    echo "Usuario referido creado con ID: {$referred->id}\n";
    echo "Referred_by_id en nuevo usuario: " . ($referred->referred_by_id ?? 'NULL') . "\n";
    
    // Verificar Referral record
    $referralRecord = Referral::where('referred_email', $request->email)->first();
    if ($referralRecord) {
        echo "Registro Referral creado: Status='{$referralRecord->status}'\n";
    } else {
        echo "Error: No se creó registro en tabla referrals.\n";
    }

    // Verificar si UserObserver añadió sesiones
    $latestContractAfter = ContractedTreatment::where('user_id', $referrer->id)->latest()->first();
    $sessionsAfter = $latestContractAfter ? $latestContractAfter->sessions : 'Ninguno';
    echo "Sesiones del referidor después: {$sessionsAfter}\n";
    
    // Verificar AccountingRecord
    $accountingRecord = AccountingRecord::where('description', 'like', '%Prueba Consola%')->first();
    if ($accountingRecord) {
        echo "Comisión pagada a staff: \${$accountingRecord->amount} (User ID: {$accountingRecord->user_id})\n";
    } else {
        echo "No se generó pago de comisión (es normal si el referido no tenía cita previa atendida).\n";
    }
} else {
    echo "Fallo al crear usuario.\n";
}

echo "=== FIN DE LA PRUEBA ===\n";

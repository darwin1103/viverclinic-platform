<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Referral;
use App\Models\AccountingRecord;
use App\Models\ContractedTreatment;

echo "=== INICIANDO PRUEBA DE COMPRA DE REFERIDO ===\n";

// Encontrar al referido creado anteriormente
$referredEmail = 'prueba.consola1777597155@mail.com'; // Necesito un email genérico o simplemente el último referido
$referral = Referral::where('status', 'Pendiente')->latest()->first();

if (!$referral) {
    echo "No hay referidos en estado Pendiente para probar.\n";
    exit(1);
}

$referredUser = User::where('email', $referral->referred_email)->first();
$referrer = User::find($referral->referrer_id);

if (!$referredUser || !$referrer) {
    echo "Error: Usuarios no encontrados.\n";
    exit(1);
}

echo "Referido: {$referredUser->name} (Email: {$referredUser->email})\n";
echo "Referente: {$referrer->name}\n";

$latestContractBefore = ContractedTreatment::where('user_id', $referrer->id)->latest()->first();
$sessionsBefore = $latestContractBefore ? $latestContractBefore->sessions : 0;
echo "Sesiones del referente antes de la compra: {$sessionsBefore}\n";

// Simular la creación de un ContractedTreatment (compra)
echo "Simulando primera compra de tratamiento por el referido...\n";
$contract = ContractedTreatment::create([
    'user_id' => $referredUser->id,
    'treatment_id' => 1, // Asumimos que existe
    'branch_id' => 1,
    'sessions' => 5,
    'total_price' => 500.00,
    'selected_zones' => json_encode(['Cara']),
    'days_between_sessions' => 30,
    'terms_acepted' => true,
    'is_pregnant' => false,
    'payment_status' => 'paid',
    'status' => 'active'
]);

echo "Tratamiento creado con ID: {$contract->id} por $500.00\n";

// Verificar el estado del Referral
$referral->refresh();
echo "Nuevo estado del Referral: {$referral->status}\n";

// Verificar sesiones del referente
$latestContractAfter = ContractedTreatment::where('user_id', $referrer->id)->latest()->first();
$sessionsAfter = $latestContractAfter ? $latestContractAfter->sessions : 0;
echo "Sesiones del referente después de la compra: {$sessionsAfter}\n";

// Verificar AccountingRecord
$accountingRecord = AccountingRecord::where('description', 'like', '%Comisión por referido de paciente ' . $referredUser->name . '%')->first();
if ($accountingRecord) {
    echo "Comisión pagada a staff: \${$accountingRecord->amount} (User ID: {$accountingRecord->user_id})\n";
} else {
    echo "No se generó pago de comisión (es normal si el referente no tenía cita previa atendida).\n";
}

echo "=== FIN DE LA PRUEBA ===\n";

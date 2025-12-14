<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreTreatmentRequest;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Mail\NewTreatmentContracted;
use Illuminate\Support\Facades\Mail;
use App\Models\TreatmentOrder;
use Illuminate\Support\Facades\DB;

class TreatmentController extends Controller
{
    /**
     * Muestra los tratamientos disponibles para una sucursal específica.
     */
    public function index()
    {
        // Cargar solo los tratamientos activos asociados a esta sucursal
        $user = Auth::user();
        $branch = $user->patientProfile->branch;
        $treatments = Treatment::where('active', true)
            ->whereHas('packages', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->get();
        return view('client.treatment.index', compact('branch', 'treatments'));
    }


    public function show(Treatment $treatment)
    {

        $user = Auth::user();
        $branch = $user->patientProfile->branch;

        $isAvailableInBranch = $treatment->packages()
            ->where('branch_id', $branch->id)
            ->exists();

        if (!$isAvailableInBranch) {
            abort(404);
        }

        $packages = $treatment->packages()
            ->where('branch_id', $branch->id)
            ->with(['installments' => function($q) {
                $q->orderBy('installment_number');
            }])
            ->select('id', 'name', 'price', 'big_zones', 'mini_zones', 'allow_installments')
            ->get();

        $additionalZones = [
            [
                'id' => 'mini',
                'name' => 'Mini zona adicional',
                'price' => $treatment->price_additional_mini_zone,
            ],
            [
                'id' => 'big',
                'name' => 'Zona grande adicional',
                'price' => $treatment->price_additional_zone,
            ],
        ];

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        $treatment = $treatment;

        return view('client.treatment.show', compact(
            'packages',
            'additionalZones',
            'bigZones',
            'smallZones',
            'miniZones',
            'treatment'
        ));

    }

public function store(StoreTreatmentRequest $request)
    {
        $validatedData = $request->validated();
        $user = Auth::user();
        $branch = $user->patientProfile->branch;

        // Determinar intención de pago: 'full' (Total) o 'installment' (Cuotas)
        $paymentType = $request->input('payment_type', 'full');

        DB::beginTransaction();

        try {
            // --- 1. Validar y Obtener Paquetes ---
            $treatment = Treatment::findOrFail($validatedData['treatment_id']);
            $submittedPackageIds = array_keys($validatedData['package'] ?? []);

            // Traemos paquetes con sus cuotas
            $validPackages = $treatment->packages()
                ->where('branch_id', $branch->id)
                ->whereIn('id', $submittedPackageIds)
                ->with(['installments' => function($q) {
                    $q->orderBy('installment_number');
                }])
                ->get()
                ->keyBy('id');

            if (count($submittedPackageIds) !== $validPackages->count()) {
                throw ValidationException::withMessages(['package' => 'Paquete seleccionado no válido.']);
            }

            // --- 2. Calcular Totales y Estructuras JSON ---
            $totalContractPrice = 0;
            $contractedPackages = [];
            $contractedAdditionals = [];

            // A) Paquetes
            foreach (($validatedData['package'] ?? []) as $id => $quantity) {
                $pkg = $validPackages->get($id);
                $lineTotal = $pkg->price * $quantity;
                $totalContractPrice += $lineTotal;

                $contractedPackages[] = [
                    'id' => $pkg->id,
                    'name' => $pkg->name,
                    'quantity' => (int)$quantity,
                    'price_at_purchase' => $pkg->price
                ];
            }

            // B) Adicionales
            // (Asumimos precios fijos definidos en el tratamiento)
            $additionalPrices = ['mini' => $treatment->price_additional_mini_zone, 'big' => $treatment->price_additional_zone];
            $additionalNames = ['mini' => 'Mini zona adicional', 'big' => 'Zona grande adicional'];

            foreach (($validatedData['additional'] ?? []) as $type => $quantity) {
                if($quantity > 0) {
                    $lineTotal = $additionalPrices[$type] * $quantity;
                    $totalContractPrice += $lineTotal;

                    $contractedAdditionals[] = [
                        'id' => $type,
                        'name' => $additionalNames[$type],
                        'quantity' => (int)$quantity,
                        'price_at_purchase' => $additionalPrices[$type]
                    ];
                }
            }

            // C) Zonas Seleccionadas
            $selectedZones = $validatedData['selected_zones'] ?? ['big' => [], 'mini' => []];
            if (!empty($validatedData['another_big_zone'])) $selectedZones['big'][] = $validatedData['another_big_zone'];
            if (!empty($validatedData['another_mini_zone'])) $selectedZones['mini'][] = $validatedData['another_mini_zone'];


            // --- 3. Crear el Registro Principal (Contrato) ---
            $contractedTreatment = ContractedTreatment::create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'treatment_id' => $treatment->id,
                'contracted_packages' => $contractedPackages,
                'contracted_additionals' => $contractedAdditionals,
                'selected_zones' => $selectedZones,
                'total_price' => $totalContractPrice,
                'status' => 'Pending',
                'sessions' => $treatment->sessions,
                'days_between_sessions' => $treatment->days_between_sessions,
                'terms_acepted' => ($validatedData['termsConditions'] == 1),
                'is_pregnant' => ($validatedData['notPregnant'] ?? 0) == 1,
            ]);


            // --- 4. Generar Cuotas en Base de Datos ---
            // Iteramos los paquetes comprados para generar las cuotas asociadas al contrato
            foreach (($validatedData['package'] ?? []) as $id => $quantity) {
                $pkg = $validPackages->get($id);

                // Solo si el paquete permite cuotas y tiene definición de ellas
                if ($pkg->allow_installments && $pkg->installments->isNotEmpty()) {
                    foreach ($pkg->installments as $inst) {
                        // Creamos la cuota en el contrato multiplicada por la cantidad de paquetes
                        $contractedTreatment->installments()->create([
                            'installment_number' => $inst->installment_number,
                            'price' => $inst->price * $quantity,
                            'status' => 'PENDING'
                        ]);
                    }
                }
            }


            // --- 5. PROCESAMIENTO DEL PAGO INICIAL ---

            $amountToPay = 0;
            $paymentDescription = '';
            $paidInstallmentIds = [];
            $hasInstallmentsConfigured = $contractedTreatment->installments()->exists();

            if ($paymentType === 'installment' && $hasInstallmentsConfigured) {
                // Caso: Pago de Primera Cuota

                // a) Sumar 1ra cuota de paquetes que TIENEN cuotas
                $firstInstallments = $contractedTreatment->installments()
                    ->where('installment_number', 1)
                    ->get();

                $amountToPay += $firstInstallments->sum('price');

                // Marcar estas cuotas como pagadas en memoria (para la orden)
                foreach ($firstInstallments as $inst) {
                    $inst->update(['status' => 'PAID', 'paid_at' => now()]);
                    $paidInstallmentIds[] = $inst->id;
                }

                // b) Sumar PRECIO TOTAL de paquetes que NO tienen cuotas
                // Recorremos lo comprado nuevamente para detectar qué no generó cuota
                foreach (($validatedData['package'] ?? []) as $id => $quantity) {
                    $pkg = $validPackages->get($id);
                    if (!$pkg->allow_installments || $pkg->installments->isEmpty()) {
                        // Este paquete se debe pagar full ahora mismo
                        $amountToPay += ($pkg->price * $quantity);
                    }
                }

                // c) Sumar PRECIO TOTAL de Adicionales
                foreach (($validatedData['additional'] ?? []) as $type => $quantity) {
                    if($quantity > 0) {
                        $amountToPay += ($additionalPrices[$type] * $quantity);
                    }
                }

                $paymentDescription = "Pago Inicial (Cuotas + Saldos)";

            } else {
                // Caso: Pago Total (Full)
                // O fallback si el usuario intentó pagar cuotas en un paquete que no tiene

                $amountToPay = $totalContractPrice;
                $paymentDescription = "Pago Total de Contrato";

                // Actualizar estado del contrato a Pagado
                $contractedTreatment->update(['status' => 'Paid']);

                // Si existen cuotas generadas, marcarlas todas como pagadas
                if ($hasInstallmentsConfigured) {
                    foreach ($contractedTreatment->installments as $inst) {
                        $inst->update(['status' => 'PAID', 'paid_at' => now()]);
                        $paidInstallmentIds[] = $inst->id;
                    }
                }
            }

            // --- 6. Crear Orden de Pago ---
            TreatmentOrder::create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'contracted_treatment_id' => $contractedTreatment->id,
                'total' => $amountToPay,
                'status' => 'PAID', // Asumimos pago exitoso inmediato
                'payment_method' => 'Initial Purchase',
                'payment_description' => $paymentDescription,
                'paid_installments_ids' => $paidInstallmentIds
            ]);

            DB::commit();

            // Enviar correo
            try {
                Mail::to($user)->queue(new NewTreatmentContracted($contractedTreatment));
            } catch (\Exception $e) { \Log::error('Mail error: ' . $e->getMessage()); }

            return redirect()->route('client.contracted-treatment.index')
                ->with('success', '¡Tratamiento contratado y pago registrado correctamente!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error en la compra: ' . $e->getMessage()])->withInput();
        }
    }
}

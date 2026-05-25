<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreTreatmentRequest;
use App\Mail\NewTreatmentContracted;
use App\Mail\TreatmentOrderConfirmation;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use App\Models\Setting;
use App\Models\Treatment;
use App\Models\TreatmentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class TreatmentController extends Controller
{
    /**
     * Muestra los tratamientos disponibles para una sucursal específica.
     */
    public function index()
    {
        $user = Auth::user();
        $profile = $user->patientProfile;
        if (!$profile || !$profile->branch_id) {
            return view('client.treatment.index', ['treatments' => collect(), 'branch' => null])
                ->withErrors('No tienes una sucursal asignada a tu perfil.');
        }

        $branch = $profile->branch;
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
        $profile = $user->patientProfile;
        if (!$profile || !$profile->branch_id) {
            return redirect()->route('client.treatment.index')->withErrors('No tienes una sucursal asignada a tu perfil.');
        }
        $branch = $profile->branch;

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
            ->select('id', 'name', 'price', 'big_zones', 'mini_zones', 'allow_installments', 'installment_conditions')
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

        $wompiPublicKey = Setting::get('wompi_public_key');

        return view('client.treatment.show', compact(
            'packages',
            'additionalZones',
            'bigZones',
            'smallZones',
            'miniZones',
            'treatment',
            'wompiPublicKey',
        ));

    }

    public function store(StoreTreatmentRequest $request)
    {
        // 1. Validaciones Adicionales de Pago
        $request->validate([
            'payment_type' => 'required|in:full,installment',
            'payment_method' => 'required|in:CASH,TRANSFER,WOMPI',
            'payment_receipt' => 'nullable|required_if:payment_method,TRANSFER|image|max:4096',
        ], [
            // Mensajes para payment_type
            'payment_type.required' => 'Debe seleccionar la modalidad de pago (Total o Cuota).',
            'payment_type.in' => 'La modalidad de pago seleccionada no es válida.',

            // Mensajes para payment_method
            'payment_method.required' => 'Debe seleccionar un método de pago.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',

            // Mensajes para payment_receipt
            'payment_receipt.required_if' => 'Debe indicar el comprobante de pago si selecciona Transferencia Bancaria.',
            'payment_receipt.image' => 'El comprobante debe ser un archivo de imagen (JPG, PNG, etc.).',
            'payment_receipt.max' => 'La imagen del comprobante no debe pesar más de 4MB.',
        ]);

        $validatedData = $request->validated();
        $user = Auth::user();
        $profile = $user->patientProfile;
        if (!$profile || !$profile->branch_id) {
            return redirect()->route('client.treatment.index')->withErrors('No tienes una sucursal asignada a tu perfil.');
        }
        $branch = $profile->branch;
        $paymentType = $request->payment_type;

        DB::beginTransaction();

        try {
            // --- A. LÓGICA DE TRATAMIENTO (Igual que antes) ---
            $treatment = Treatment::findOrFail($validatedData['treatment_id']);
            $submittedPackageIds = array_keys($validatedData['package'] ?? []);

            $validPackages = $treatment->packages()
                ->where('branch_id', $branch->id)
                ->whereIn('id', $submittedPackageIds)
                ->with(['installments' => fn($q) => $q->orderBy('installment_number')])
                ->get()
                ->keyBy('id');

            if (count($submittedPackageIds) !== $validPackages->count()) {
                throw ValidationException::withMessages(['package' => 'Paquete inválido.']);
            }

            // Calcular Totales del Contrato
            $totalContractPrice = 0;
            $contractedPackages = [];
            $contractedAdditionals = [];

            // Paquetes
            foreach (($validatedData['package'] ?? []) as $id => $quantity) {
                $pkg = $validPackages->get($id);
                $lineTotal = $pkg->price * $quantity;
                $totalContractPrice += $lineTotal;
                $contractedPackages[] = [
                    'id' => $pkg->id, 'name' => $pkg->name,
                    'quantity' => (int)$quantity, 'price_at_purchase' => $pkg->price
                ];
            }

            // Adicionales
            $additionalPrices = ['mini' => $treatment->price_additional_mini_zone, 'big' => $treatment->price_additional_zone];
            $additionalNames = ['mini' => 'Mini zona adicional', 'big' => 'Zona grande adicional'];
            foreach (($validatedData['additional'] ?? []) as $type => $quantity) {
                if($quantity > 0) {
                    $lineTotal = $additionalPrices[$type] * $quantity;
                    $totalContractPrice += $lineTotal;
                    $contractedAdditionals[] = [
                        'id' => $type, 'name' => $additionalNames[$type],
                        'quantity' => (int)$quantity, 'price_at_purchase' => $additionalPrices[$type]
                    ];
                }
            }

            // Zonas
            $selectedZones = $validatedData['selected_zones'] ?? ['big' => [], 'mini' => []];
            if (!empty($validatedData['another_big_zone'])) $selectedZones['big'][] = $validatedData['another_big_zone'];
            if (!empty($validatedData['another_mini_zone'])) $selectedZones['mini'][] = $validatedData['another_mini_zone'];

            // Crear Contrato
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

            // Generar Cuotas DB
            foreach (($validatedData['package'] ?? []) as $id => $quantity) {
                $pkg = $validPackages->get($id);
                if ($pkg->allow_installments && $pkg->installments->isNotEmpty()) {
                    foreach ($pkg->installments as $inst) {
                        $contractedTreatment->installments()->create([
                            'installment_number' => $inst->installment_number,
                            'price' => $inst->price * $quantity,
                            'status' => 'PENDING'
                        ]);
                    }
                }
            }

            // --- B. LÓGICA DE PAGO (Integrada) ---

            $amountToPay = 0;
            $paymentDescription = '';
            $targetInstallments = collect([]); // Cuotas a marcar como pagadas/afectadas

            if ($paymentType === 'installment' && $contractedTreatment->hasInstallments()) {
                // Pagar 1ra Cuota
                $firstInstallments = $contractedTreatment->installments()->where('installment_number', 1)->get();

                // Sumar cuotas + Paquetes sin cuotas + Adicionales
                $installmentsTotal = $firstInstallments->sum('price');

                $noInstallmentPackagesTotal = 0;
                foreach (($validatedData['package'] ?? []) as $id => $quantity) {
                    $pkg = $validPackages->get($id);
                    if (!$pkg->allow_installments || $pkg->installments->isEmpty()) {
                        $noInstallmentPackagesTotal += ($pkg->price * $quantity);
                    }
                }

                $additionalsTotal = 0;
                foreach (($validatedData['additional'] ?? []) as $type => $quantity) {
                    if($quantity > 0) $additionalsTotal += ($additionalPrices[$type] * $quantity);
                }

                $amountToPay = $installmentsTotal + $noInstallmentPackagesTotal + $additionalsTotal;
                $paymentDescription = "Pago Inicial (Cuota 1 + Adicionales) - {$treatment->name}";
                $targetInstallments = $firstInstallments; // Colección de cuotas afectadas

            } else {
                // Pago Total
                $amountToPay = $totalContractPrice;
                $paymentDescription = "Pago Total - {$treatment->name}";
                if ($contractedTreatment->hasInstallments()) {
                    $targetInstallments = $contractedTreatment->installments;
                }
            }

            // --- C. MANEJO POR MÉTODO DE PAGO ---

            // CASO 1: WOMPI (No creamos orden aún en DB, redirigimos a vista de Widget)
            if ($request->payment_method === 'WOMPI') {
                DB::commit(); // Guardamos el Contrato y las Cuotas PENDING

                // Renderizamos la vista de Wompi directamente
                return $this->renderWompiView($amountToPay, $paymentDescription, $user, $contractedTreatment, $paymentType, $targetInstallments->pluck('id')->toArray());
            }

            // CASO 2: TRANSFERENCIA / EFECTIVO (Creamos Orden Inmediata)

            $orderStatus = 'Pago por verificar';
            $paymentStatus = 'PENDING';
            $receiptPath = null;

            if ($request->hasFile('payment_receipt')) {
                $receiptPath = $request->file('payment_receipt')->store('treatment_receipts', 'public');
            }

            $order = TreatmentOrder::create([
                'user_id' => $user->id,
                'branch_id' => $branch->id,
                'contracted_treatment_id' => $contractedTreatment->id,
                'total' => $amountToPay,
                'status' => $orderStatus,
                'payment_method' => $request->payment_method === 'CASH' ? 'Efectivo' : 'Transferencia',
                'payment_status' => $paymentStatus,
                'payment_description' => $paymentDescription,
                'payment_receipt' => $receiptPath,
                'currency' => 'COP',
                'customer_email' => $user->email,
                'paid_installments_ids' => collect($targetInstallments)->pluck('id')->toArray()
            ]);

            DB::commit();

            // Enviar correos
            try {
                Mail::to($user)->queue(new NewTreatmentContracted($contractedTreatment));
                Mail::to($user)->queue(new TreatmentOrderConfirmation($order));
            } catch (\Exception $e) {}

            return redirect()->route('client.treatment.payment.thank-you', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error: ' . $e->getMessage()])->withInput();
        }
    }

    // Helper para Wompi (mismo de antes, asegúrate de tenerlo en la clase)
    private function renderWompiView($amount, $description, $user, $contractedTreatment, $paymentType, $installmentIds)
    {
        $pubKey = Setting::get('wompi_public_key');
        $secret = Setting::get('wompi_integrity_secret');

        $reference = 'TRT-' . time() . '-' . Str::random(4);
        $amountInCents = $amount * 100;
        $currency = 'COP';
        $integritySignature = hash('sha256', $reference . $amountInCents . $currency . $secret);

        // Guardar sesión para retorno
        session()->put('wompi_pending_treatment_payment', [
            'contracted_treatment_id' => $contractedTreatment->id,
            'payment_type' => $paymentType,
            'installment_ids' => $installmentIds,
            'description' => $description,
            'amount' => $amount,
            'reference' => $reference
        ]);

        $redirectUrl = (config('app.env') == 'production') ? route('client.treatment.payment.result') : 'https://dev.viverclinic.com/treatment/payment/thank-you/'; // usar url real ***

        $wompiData = [
            'public_key' => $pubKey,
            'currency' => $currency,
            'amount_in_cents' => $amountInCents,
            'reference' => $reference,
            'signature' => $integritySignature,
            'redirect_url' => $redirectUrl,
            'email' => $user->email,
            'full_name' => $user->name,
        ];

        return view('client.payment.wompi-checkout', compact('wompiData', 'amount', 'description'));
    }

}

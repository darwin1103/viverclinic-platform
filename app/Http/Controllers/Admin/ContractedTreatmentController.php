<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchTreatment;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use App\Models\TreatmentOrder;
use App\Models\ContractedTreatmentNote;
use App\Models\ContractedTreatmentInstallment;
use App\Models\PackageUpgrade;
use App\Models\Setting;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Mail\TreatmentOrderConfirmation;
use Illuminate\Support\Facades\Mail;

class ContractedTreatmentController extends Controller
{

    public function index(Request $request)
    {
        $query = ContractedTreatment::with(['user', 'branch', 'treatment'])
                    ->latest(); // Ordenar por más reciente

        // Filter by search term (client name or email)
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }elseif(session('selected_branch_id')){
            $query->where('branch_id', session('selected_branch_id'));

        }

        // Filter by treatment
        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        $contractedTreatments = $query->paginate(15)->withQueryString();

        // Data for filters
        $treatments = Treatment::where('active', true)->orderBy('name')->get();

        $branches = Branch::all();

        if ($request->has('branch_id')) {
            if ($request->filled('branch_id')) {
                session(['selected_branch_id' => $request->input('branch_id')]);
            } else {
                session()->forget('selected_branch_id');
            }
        }
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.contracted-treatment.index', compact(
            'contractedTreatments',
            'treatments',
            'branches',
            'selectedBranchID'
        ));

    }

    public function show(ContractedTreatment $contractedTreatment)
    {

        $contractedTreatment->load(['user', 'branch', 'treatment', 'installments', 'orders', 'notes.user']);

        return view('admin.contracted-treatment.show', compact('contractedTreatment'));

    }

    /**
     * Guardar una nueva nota interna.
     */
    public function storeNote(Request $request, ContractedTreatment $contractedTreatment)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $contractedTreatment->notes()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return back()->with('success', 'Nota agregada correctamente.');
    }

    /**
     * Actualizar una nota existente.
     */
    public function updateNote(Request $request, ContractedTreatmentNote $note)
    {
        if (!auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER'])) {
            abort(403);
        }

        $request->validate([
            'content' => 'required|string|max:2000',
        ]);

        $note->update([
            'content' => $request->content,
        ]);

        return back()->with('success', 'Nota actualizada correctamente.');
    }

    /**
     * Eliminar una nota.
     */
    public function destroyNote(ContractedTreatmentNote $note)
    {
        if (!auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER'])) {
            abort(403);
        }

        $note->delete();

        return back()->with('success', 'Nota eliminada correctamente.');
    }

    /**
     * Aprobar un pago pendiente (Transferencia o Efectivo)
     */
    public function approvePayment(TreatmentOrder $order)
    {
        if ($order->status === 'Pago completado') {
            return back()->with('info', 'Esta orden ya fue aprobada.');
        }

        DB::beginTransaction();
        try {
            // 1. Actualizar estado de la Orden
            $order->update([
                'status' => 'Pago completado',
                'payment_status' => 'APPROVED',
            ]);

            $contractedTreatment = $order->contractedTreatment;

            // Check if this contracted treatment has a pending package upgrade
            $packageUpgrade = $contractedTreatment?->packageUpgrade;
            $accountingCategory = 'Tratamientos';
            if ($packageUpgrade && $packageUpgrade->payment_status === 'PENDING') {
                $packageUpgrade->update(['payment_status' => 'APPROVED']);
                $accountingCategory = 'Agrandamiento';
            }

            // 2. Actualizar las cuotas asociadas a esta orden
            // Usamos el campo JSON 'paid_installments_ids' que guardamos al crear la orden
            if (!empty($order->paid_installments_ids)) {
                $contractedTreatment->installments()
                    ->whereIn('id', $order->paid_installments_ids)
                    ->update([
                        'status' => 'PAID',
                        'paid_at' => now()
                    ]);

                // 3. Verificar si se completó todo el contrato
                $pendingCount = $contractedTreatment->installments()->where('status', 'PENDING')->count();
                if ($pendingCount === 0) {
                    $contractedTreatment->update(['status' => 'Paid']);
                }
            } else {
                // Si no hay IDs de cuotas (ej. pago total antiguo o lógica diferida),
                // asumimos lógica por defecto o pago total
                if($contractedTreatment && $contractedTreatment->status !== 'Paid'){
                     $contractedTreatment->update(['status' => 'Paid']);
                }
            }

            // Opcional: Enviar correo de aprobación
            // Mail::to($order->user)->queue(new TreatmentOrderConfirmation($order));

            // Procesar recompensa de referido (si aplica)
            ReferralService::processReward($order->user);

            // Register income in accounting
            \App\Models\AccountingRecord::create([
                'branch_id' => $order->branch_id ?? session('selected_branch_id') ?? 1,
                'user_id' => $order->user_id,
                'type' => 'income',
                'amount' => $order->total,
                'description' => 'Pago aprobado: ' . ($order->contractedTreatment?->treatment?->name ?? 'Tratamiento') . ' - Paciente: ' . ($order->user->name ?? 'N/A'),
                'category' => $accountingCategory,
                'reference_id' => $order->id,
                'reference_type' => TreatmentOrder::class,
            ]);

            DB::commit();
            return back()->with('success', 'Pago aprobado correctamente. Las cuotas han sido actualizadas.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Rechazar un pago
     */
    public function rejectPayment(Request $request, TreatmentOrder $order)
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        if ($order->status !== 'Pago por verificar') {
            return back()->with('error', 'No se puede rechazar esta orden en su estado actual.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'Cancelado',
                'payment_status' => 'DECLINED',
                'payment_description' => $order->payment_description . ' [Rechazado: ' . ($request->reason ?? 'Sin motivo') . ']'
            ]);

            $packageUpgrade = $order->contractedTreatment?->packageUpgrade;
            if ($packageUpgrade && $packageUpgrade->payment_status === 'PENDING') {
                $contractedTreatment = $order->contractedTreatment;

                // Revert contracted packages, selected zones, and total price
                $contractedTreatment->update([
                    'contracted_packages' => $packageUpgrade->old_package_data,
                    'selected_zones' => $packageUpgrade->old_selected_zones,
                    'total_price' => $contractedTreatment->total_price - $packageUpgrade->price_difference,
                ]);

                // Create audit note for rejection and revert
                $contractedTreatment->notes()->create([
                    'user_id' => auth()->id(),
                    'content' => "Agrandamiento de paquete RECHAZADO por " . auth()->user()->name . ". El tratamiento ha sido revertido al paquete original. Motivo: " . ($request->reason ?? 'Sin motivo'),
                ]);

                // Delete the package upgrade record
                $packageUpgrade->delete();
            }

            DB::commit();
            return back()->with('success', 'El pago ha sido rechazado.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al rechazar el pago: ' . $e->getMessage());
        }
    }

    public function edit(ContractedTreatment $contractedTreatment)
    {
        if (!auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER'])) {
            abort(403);
        }

        $contractedTreatment->load(['user', 'branch', 'treatment']);

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        return view('admin.contracted-treatment.edit', compact(
            'contractedTreatment',
            'bigZones',
            'smallZones',
            'miniZones'
        ));
    }

    public function update(Request $request, ContractedTreatment $contractedTreatment)
    {
        if (!auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER'])) {
            abort(403);
        }

        $request->validate([
            'sessions' => 'required|integer|min:1',
            'days_between_sessions' => 'required|integer|min:0',
            'selected_zones' => 'nullable|array',
            'another_big_zone' => 'nullable|string|max:100',
            'another_mini_zone' => 'nullable|string|max:100',
        ]);

        $oldSessions = $contractedTreatment->sessions;
        $oldDays = $contractedTreatment->days_between_sessions;
        $oldZones = $contractedTreatment->selected_zones ?? ['big' => [], 'mini' => []];

        $selectedZones = $request->selected_zones ?? ['big' => [], 'mini' => []];
        if (!empty($request->another_big_zone)) {
            if (!isset($selectedZones['big'])) {
                $selectedZones['big'] = [];
            }
            $selectedZones['big'][] = $request->another_big_zone;
        }
        if (!empty($request->another_mini_zone)) {
            if (!isset($selectedZones['mini'])) {
                $selectedZones['mini'] = [];
            }
            $selectedZones['mini'][] = $request->another_mini_zone;
        }

        // Ensure keys big and mini exist and are arrays
        if (!isset($selectedZones['big'])) {
            $selectedZones['big'] = [];
        }
        if (!isset($selectedZones['mini'])) {
            $selectedZones['mini'] = [];
        }

        $contractedTreatment->update([
            'sessions' => $request->sessions,
            'days_between_sessions' => $request->days_between_sessions,
            'selected_zones' => $selectedZones,
        ]);

        // Audit changes
        $changes = [];
        if ($oldSessions != $contractedTreatment->sessions) {
            $changes[] = "- Sesiones: de {$oldSessions} a {$contractedTreatment->sessions}";
        }
        if ($oldDays != $contractedTreatment->days_between_sessions) {
            $changes[] = "- Días entre sesiones: de {$oldDays} a {$contractedTreatment->days_between_sessions}";
        }

        $oldBig = $oldZones['big'] ?? [];
        $newBig = $selectedZones['big'] ?? [];
        $oldMini = $oldZones['mini'] ?? [];
        $newMini = $selectedZones['mini'] ?? [];

        // Sort arrays to compare properly
        sort($oldBig);
        sort($newBig);
        sort($oldMini);
        sort($newMini);

        if ($oldBig != $newBig) {
            $changes[] = "- Zonas Grandes/Pequeñas: de [" . implode(', ', $oldBig) . "] a [" . implode(', ', $newBig) . "]";
        }
        if ($oldMini != $newMini) {
            $changes[] = "- Zonas Mini: de [" . implode(', ', $oldMini) . "] a [" . implode(', ', $newMini) . "]";
        }

        if (!empty($changes)) {
            $contractedTreatment->notes()->create([
                'user_id' => auth()->id(),
                'content' => "Tratamiento editado por " . auth()->user()->name . ":\n" . implode("\n", $changes),
            ]);
        }

        return redirect()->route('admin.contracted-treatment.show', $contractedTreatment->id)
            ->with('success', 'Tratamiento actualizado correctamente.');
    }

    public function toggleInstallmentStatus(ContractedTreatmentInstallment $installment)
    {
        if (!auth()->user()->hasRole(['SUPER_ADMIN', 'OWNER'])) {
            abort(403);
        }

        $contractedTreatment = $installment->contractedTreatment;
        $oldStatus = $installment->status;
        $newStatus = $oldStatus == 'PAID' ? 'PENDING' : 'PAID';
        $paidAt = $newStatus == 'PAID' ? now() : null;

        $installment->update([
            'status' => $newStatus,
            'paid_at' => $paidAt
        ]);

        // Recalculate treatment status
        $totalInstallments = $contractedTreatment->installments()->count();
        $paidInstallments = $contractedTreatment->installments()->where('status', 'PAID')->count();

        if ($totalInstallments > 0) {
            if ($paidInstallments === $totalInstallments) {
                if ($contractedTreatment->status !== 'Paid') {
                    $contractedTreatment->update(['status' => 'Paid']);
                }
            } else {
                if ($contractedTreatment->status === 'Paid') {
                    $contractedTreatment->update(['status' => 'Pending']);
                }
            }
        }

        // Add internal note
        $contractedTreatment->notes()->create([
            'user_id' => auth()->id(),
            'content' => "Cuota #{$installment->installment_number} marcada como " . ($newStatus == 'PAID' ? 'PAGADA' : 'PENDIENTE') . " manualmente por " . auth()->user()->name . ".",
        ]);

        return back()->with('success', "Estado de la cuota #{$installment->installment_number} actualizado correctamente.");
    }

    public function upgradeForm(ContractedTreatment $contractedTreatment)
    {
        $contractedTreatment->load(['user', 'branch', 'treatment', 'appointments']);

        if (!$contractedTreatment->canBeUpgraded()) {
            return redirect()->route('admin.contracted-treatment.show', $contractedTreatment->id)
                ->with('error', 'El tratamiento no cumple con las condiciones para un agrandamiento de paquete (requiere primera cita atendida con empleada asignada y no tener upgrades previos).');
        }

        $currentPackage = !empty($contractedTreatment->contracted_packages) ? $contractedTreatment->contracted_packages[0] : null;
        if (!$currentPackage) {
            return redirect()->route('admin.contracted-treatment.show', $contractedTreatment->id)
                ->with('error', 'No se encontró información del paquete original contratado.');
        }

        $currentPackagePrice = $currentPackage['price_at_purchase'];

        $availablePackages = BranchTreatment::where('treatment_id', $contractedTreatment->treatment_id)
            ->where('branch_id', $contractedTreatment->branch_id)
            ->where('price', '>', $currentPackagePrice)
            ->orderBy('price', 'asc')
            ->get();

        if ($availablePackages->isEmpty()) {
            return redirect()->route('admin.contracted-treatment.show', $contractedTreatment->id)
                ->with('error', 'No hay paquetes superiores disponibles para este tratamiento en esta sucursal.');
        }

        $firstAppointment = $contractedTreatment->appointments()->where('session_number', 1)->first();
        $staffUser = $firstAppointment?->staff;

        $commissionType = Setting::get('upgrade_commission_type', 'fixed');
        $commissionValue = (float) Setting::get('upgrade_commission_value', '0');

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        return view('admin.contracted-treatment.upgrade', compact(
            'contractedTreatment',
            'currentPackage',
            'availablePackages',
            'staffUser',
            'commissionType',
            'commissionValue',
            'bigZones',
            'smallZones',
            'miniZones'
        ));
    }

    public function processUpgrade(Request $request, ContractedTreatment $contractedTreatment)
    {
        $request->validate([
            'new_package_id' => 'required|exists:branch_treatment,id',
            'selected_zones' => 'nullable|array',
            'another_big_zone' => 'nullable|string|max:100',
            'another_mini_zone' => 'nullable|string|max:100',
            'payment_method' => 'required|in:CASH,TRANSFER',
            'payment_receipt' => 'nullable|required_if:payment_method,TRANSFER|image|max:4096',
        ]);

        if (!$contractedTreatment->canBeUpgraded()) {
            return redirect()->route('admin.contracted-treatment.show', $contractedTreatment->id)
                ->with('error', 'El tratamiento no cumple con las condiciones para un agrandamiento de paquete.');
        }

        $newPackage = BranchTreatment::findOrFail($request->new_package_id);

        if ($newPackage->treatment_id !== $contractedTreatment->treatment_id || $newPackage->branch_id !== $contractedTreatment->branch_id) {
            return back()->withErrors(['new_package_id' => 'El paquete seleccionado no pertenece al mismo tratamiento o sucursal.'])->withInput();
        }

        $currentPackage = !empty($contractedTreatment->contracted_packages) ? $contractedTreatment->contracted_packages[0] : null;
        if (!$currentPackage) {
            return back()->withErrors(['error' => 'No se encontró el paquete original contratado.'])->withInput();
        }

        $priceDifference = $newPackage->price - $currentPackage['price_at_purchase'];
        if ($priceDifference <= 0) {
            return back()->withErrors(['new_package_id' => 'El paquete seleccionado debe tener un precio mayor al paquete actual.'])->withInput();
        }

        // Calculate commission
        $commissionType = Setting::get('upgrade_commission_type', 'fixed');
        $commissionValue = (float) Setting::get('upgrade_commission_value', '0');

        if ($commissionType === 'percentage') {
            $commissionAmount = $priceDifference * ($commissionValue / 100.0);
        } else {
            $commissionAmount = $commissionValue;
        }

        $firstAppointment = $contractedTreatment->appointments()->where('session_number', 1)->first();
        $staffUserId = $firstAppointment?->staff_user_id;

        // Process zones
        $selectedZones = $request->selected_zones ?? ['big' => [], 'mini' => []];
        if (!empty($request->another_big_zone)) {
            if (!isset($selectedZones['big'])) {
                $selectedZones['big'] = [];
            }
            $selectedZones['big'][] = $request->another_big_zone;
        }
        if (!empty($request->another_mini_zone)) {
            if (!isset($selectedZones['mini'])) {
                $selectedZones['mini'] = [];
            }
            $selectedZones['mini'][] = $request->another_mini_zone;
        }

        // Ensure keys big and mini exist and are arrays
        if (!isset($selectedZones['big'])) {
            $selectedZones['big'] = [];
        }
        if (!isset($selectedZones['mini'])) {
            $selectedZones['mini'] = [];
        }

        DB::beginTransaction();
        try {
            $receiptPath = null;
            if ($request->payment_method === 'TRANSFER' && $request->hasFile('payment_receipt')) {
                $receiptPath = $request->file('payment_receipt')->store('treatment_receipts', 'public');
            }

            $paymentStatus = $request->payment_method === 'CASH' ? 'APPROVED' : 'PENDING';
            $orderStatus = $request->payment_method === 'CASH' ? 'Pago completado' : 'Pago por verificar';

            // Create PackageUpgrade
            $upgrade = PackageUpgrade::create([
                'contracted_treatment_id' => $contractedTreatment->id,
                'branch_id' => $contractedTreatment->branch_id,
                'old_package_data' => $currentPackage,
                'new_package_id' => $newPackage->id,
                'new_package_data' => [
                    'id' => $newPackage->id,
                    'name' => $newPackage->name,
                    'price' => $newPackage->price,
                    'big_zones' => $newPackage->big_zones,
                    'mini_zones' => $newPackage->mini_zones,
                ],
                'price_difference' => $priceDifference,
                'staff_user_id' => $staffUserId,
                'commission_amount' => $commissionAmount,
                'commission_type' => $commissionType,
                'commission_value' => $commissionValue,
                'payment_method' => $request->payment_method,
                'payment_status' => $paymentStatus,
                'old_selected_zones' => $contractedTreatment->selected_zones,
                'new_selected_zones' => $selectedZones,
                'processed_by' => auth()->id(),
            ]);

            // Create TreatmentOrder
            $order = TreatmentOrder::create([
                'user_id' => $contractedTreatment->user_id,
                'branch_id' => $contractedTreatment->branch_id,
                'contracted_treatment_id' => $contractedTreatment->id,
                'total' => $priceDifference,
                'status' => $orderStatus,
                'payment_method' => $request->payment_method === 'CASH' ? 'Efectivo' : 'Transferencia',
                'payment_status' => $paymentStatus,
                'payment_description' => "Agrandamiento de paquete: " . $currentPackage['name'] . " -> " . $newPackage->name,
                'payment_receipt' => $receiptPath,
                'currency' => 'COP',
                'customer_email' => $contractedTreatment->user->email,
            ]);

            // Update ContractedTreatment
            $newPackagesArray = [
                [
                    'id' => $newPackage->id,
                    'name' => $newPackage->name,
                    'quantity' => 1,
                    'price_at_purchase' => $newPackage->price,
                ]
            ];
            
            $newTotalPrice = $contractedTreatment->total_price + $priceDifference;
            
            $contractedTreatment->update([
                'contracted_packages' => $newPackagesArray,
                'selected_zones' => $selectedZones,
                'total_price' => $newTotalPrice,
            ]);

            // Register accounting entry immediately if CASH
            if ($request->payment_method === 'CASH') {
                \App\Models\AccountingRecord::create([
                    'branch_id' => $contractedTreatment->branch_id,
                    'user_id' => $contractedTreatment->user_id,
                    'type' => 'income',
                    'amount' => $priceDifference,
                    'description' => 'Agrandamiento de paquete: ' . $contractedTreatment->treatment->name . ' - Paciente: ' . $contractedTreatment->user->name,
                    'category' => 'Agrandamiento',
                    'reference_id' => $order->id,
                    'reference_type' => TreatmentOrder::class,
                ]);
            }

            // Internal Note
            $noteContent = "Agrandamiento de paquete por " . auth()->user()->name . ":\n" .
                           "- Paquete anterior: " . $currentPackage['name'] . " ($" . number_format($currentPackage['price_at_purchase'], 2) . ")\n" .
                           "- Nuevo paquete: " . $newPackage->name . " ($" . number_format($newPackage->price, 2) . ")\n" .
                           "- Diferencia a pagar: $" . number_format($priceDifference, 2) . " (Método: " . ($request->payment_method === 'CASH' ? 'Efectivo' : 'Transferencia') . ")\n" .
                           "- Empleada comisionista: " . ($firstAppointment->staff->name ?? 'N/A') . " (Comisión: $" . number_format($commissionAmount, 2) . " - " . ($commissionType === 'percentage' ? "{$commissionValue}%" : "fijo") . ")";
            
            $contractedTreatment->notes()->create([
                'user_id' => auth()->id(),
                'content' => $noteContent,
            ]);

            DB::commit();

            return redirect()->route('admin.contracted-treatment.show', $contractedTreatment->id)
                ->with('success', 'Agrandamiento de paquete procesado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al procesar el agrandamiento: ' . $e->getMessage()])->withInput();
        }
    }
}

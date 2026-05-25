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

            // 2. Actualizar las cuotas asociadas a esta orden
            // Usamos el campo JSON 'paid_installments_ids' que guardamos al crear la orden
            if (!empty($order->paid_installments_ids)) {
                $contractedTreatment = $order->contractedTreatment;

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
                if($order->contractedTreatment->status !== 'Paid'){
                     $order->contractedTreatment->update(['status' => 'Paid']);
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
                'category' => 'Tratamientos',
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

        $order->update([
            'status' => 'Cancelado',
            'payment_status' => 'DECLINED',
            'payment_description' => $order->payment_description . ' [Rechazado: ' . ($request->reason ?? 'Sin motivo') . ']'
        ]);

        // No tocamos las cuotas (siguen PENDING), así el usuario puede intentar pagar de nuevo.

        return back()->with('success', 'El pago ha sido rechazado.');
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
}

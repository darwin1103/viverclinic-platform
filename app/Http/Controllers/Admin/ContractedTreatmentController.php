<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchTreatment;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use App\Models\TreatmentOrder;
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

        $contractedTreatment->load(['user', 'branch', 'treatment', 'installments', 'orders']);

        return view('admin.contracted-treatment.show', compact('contractedTreatment'));

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
                     // Lógica de fallback si es necesario, o actualizar todo si fue pago total
                     // ...
                }
            }

            // Opcional: Enviar correo de aprobación
            // Mail::to($order->user)->queue(new TreatmentOrderConfirmation($order));

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

}

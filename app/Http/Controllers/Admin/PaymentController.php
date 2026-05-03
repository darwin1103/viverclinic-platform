<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use App\Models\Order;
use App\Models\TreatmentOrder;
use App\Models\User;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    /**
     * Display unified payments index with filters and pagination.
     */
    public function index(Request $request): View
    {
        $branchId = session('selected_branch_id');

        // --- Treatment Orders ---
        $treatmentQuery = TreatmentOrder::with(['user', 'contractedTreatment.treatment'])
            ->select(
                'id', 'user_id', 'contracted_treatment_id', 'total', 'status',
                'payment_method', 'branch_id', 'created_at',
                DB::raw("'treatment' as payment_type")
            );

        // --- Product Orders ---
        $productQuery = Order::with(['user'])
            ->select(
                'id', 'user_id',
                DB::raw('NULL as contracted_treatment_id'),
                'total', 'status',
                DB::raw("'N/A' as payment_method"),
                'branch_id', 'created_at',
                DB::raw("'product' as payment_type")
            );

        // Apply branch filter
        if ($branchId) {
            $treatmentQuery->where('branch_id', $branchId);
            $productQuery->where('branch_id', $branchId);
        }

        // Apply search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $treatmentQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
            $productQuery->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $treatmentQuery->where('status', $request->status);
            $productQuery->where('status', $request->status);
        }

        // Apply payment method filter (only for treatment orders)
        if ($request->filled('payment_method')) {
            $treatmentQuery->where('payment_method', $request->payment_method);
        }

        // Apply date filters
        if ($request->filled('from')) {
            $treatmentQuery->whereDate('created_at', '>=', $request->from);
            $productQuery->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $treatmentQuery->whereDate('created_at', '<=', $request->to);
            $productQuery->whereDate('created_at', '<=', $request->to);
        }

        // Get treatment orders with pagination
        $treatmentPayments = $treatmentQuery->latest()->get()->map(function ($item) {
            $item->concept = $item->contractedTreatment?->treatment?->name ?? 'Tratamiento';
            return $item;
        });

        $productPayments = $productQuery->latest()->get()->map(function ($item) {
            $item->concept = 'Venta de productos';
            $item->payment_method = 'N/A';
            return $item;
        });

        // Merge and sort
        $allPayments = $treatmentPayments->concat($productPayments)
            ->sortByDesc('created_at');

        // Manual pagination
        $perPage = 20;
        $page = $request->input('page', 1);
        $paginatedPayments = new \Illuminate\Pagination\LengthAwarePaginator(
            $allPayments->forPage($page, $perPage)->values(),
            $allPayments->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Get unique statuses for filter dropdown
        $statuses = TreatmentOrder::distinct()->pluck('status')->filter()->sort()->values();
        $paymentMethods = TreatmentOrder::distinct()->pluck('payment_method')->filter()->sort()->values();

        return view('admin.payments.index', [
            'payments' => $paginatedPayments,
            'statuses' => $statuses,
            'paymentMethods' => $paymentMethods,
        ]);
    }

    /**
     * Display pending payments list.
     */
    public function pending(): View
    {
        $pendingPayments = TreatmentOrder::with(['user', 'contractedTreatment.treatment'])
            ->whereIn('status', ['Pending', 'Pendiente', 'Pago por verificar'])
            ->latest()
            ->get();
            
        return view('admin.payments.pending', compact('pendingPayments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(): View
    {
        $patients = User::role('PATIENT')->select(['id', 'name'])->get();
        $contractedTreatments = ContractedTreatment::with('treatment')
            ->whereIn('status', ['Activo', 'Pending', 'In Progress'])
            ->get();
            
        return view('admin.payments.create', compact('patients', 'contractedTreatments'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'contracted_treatment_id' => 'required|exists:contracted_treatments,id',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
        ]);

        $validated['status'] = 'Pagado';
        $contractedTreatment = ContractedTreatment::find($validated['contracted_treatment_id']);
        $validated['branch_id'] = session('selected_branch_id') ?: ($contractedTreatment->branch_id ?? 1);

        TreatmentOrder::create($validated);

        return redirect()->route('admin.payments.index')->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Approve a pending payment.
     */
    public function approve(TreatmentOrder $order): RedirectResponse
    {
        if (in_array($order->status, ['Pago completado', 'Pagado'])) {
            return back()->with('info', 'Esta orden ya fue aprobada.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'Pago completado',
                'payment_status' => 'APPROVED',
            ]);

            // Update installments if they exist
            if (!empty($order->paid_installments_ids)) {
                $contractedTreatment = $order->contractedTreatment;

                $contractedTreatment->installments()
                    ->whereIn('id', $order->paid_installments_ids)
                    ->update([
                        'status' => 'PAID',
                        'paid_at' => now()
                    ]);

                $pendingCount = $contractedTreatment->installments()->where('status', 'PENDING')->count();
                if ($pendingCount === 0) {
                    $contractedTreatment->update(['status' => 'Paid']);
                }
            } else {
                if ($order->contractedTreatment && $order->contractedTreatment->status !== 'Paid') {
                    $order->contractedTreatment->update(['status' => 'Paid']);
                }
            }

            // Process referral reward if applicable
            ReferralService::processReward($order->user);

            DB::commit();
            return back()->with('success', 'Pago aprobado correctamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el pago: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending payment.
     */
    public function reject(Request $request, TreatmentOrder $order): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        if (!in_array($order->status, ['Pago por verificar', 'Pendiente', 'Pending'])) {
            return back()->with('error', 'No se puede rechazar esta orden en su estado actual.');
        }

        $order->update([
            'status' => 'Cancelado',
            'payment_status' => 'DECLINED',
            'payment_description' => $order->payment_description . ' [Rechazado: ' . ($request->reason ?? 'Sin motivo') . ']'
        ]);

        return back()->with('success', 'El pago ha sido rechazado.');
    }

    /**
     * Export payments.
     */
    public function export(): RedirectResponse
    {
        return back()->with('success', 'Exportación en construcción');
    }
}

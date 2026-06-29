<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use App\Models\AccountingRecord;
use App\Models\Order;
use App\Models\TreatmentOrder;
use App\Models\User;
use App\Services\ReferralService;
use App\Services\RepurchaseService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;
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
        $patients = User::role('PATIENT')
            ->select(['id', 'name', 'email'])
            ->orderBy('name')
            ->get()
            ->map(fn($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'email' => $p->email,
            ]);

        return view('admin.payments.create', compact('patients'));
    }

    /**
     * Get contracted treatments for a specific patient (AJAX).
     */
    public function getPatientTreatments(User $user): JsonResponse
    {
        $treatments = ContractedTreatment::with('treatment')
            ->where('user_id', $user->id)
            ->whereIn('status', ['Activo', 'Pending', 'In Progress', 'Paid'])
            ->get()
            ->map(function ($ct) {
                // Obtener nombres de los paquetes contratados
                $packageNames = collect($ct->contracted_packages)->pluck('name')->implode(', ');
                $name = $packageNames ?: ($ct->treatment->name ?? 'Tratamiento');

                return [
                    'id' => $ct->id,
                    'name' => $name,
                    'created_at' => $ct->created_at ? $ct->created_at->format('Y-m-d') : null,
                    'created_at_formatted' => $ct->created_at ? $ct->created_at->format('d/m/Y') : null,
                    'status' => $ct->status,
                    'payment_type' => $ct->payment_type ?? 'installment',
                    'remaining_balance' => $ct->remainingBalance(),
                    'total_price' => $ct->total_price,
                    'installments' => $ct->installments()
                        ->where('status', 'PENDING')
                        ->get()
                        ->map(fn($i) => [
                            'id' => $i->id,
                            'number' => $i->installment_number,
                            'price' => $i->price,
                            'label' => "Cuota #{$i->installment_number} ($" . number_format($i->price, 0, ',', '.') . ")"
                        ])
                ];
            });

        return response()->json($treatments);
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
            'paid_installments_ids' => 'nullable|array',
            'paid_installments_ids.*' => 'exists:contracted_treatment_installments,id',
        ]);

        DB::beginTransaction();
        try {
            $validated['status'] = 'Pago completado';
            $validated['payment_status'] = 'APPROVED';
            $contractedTreatment = ContractedTreatment::find($validated['contracted_treatment_id']);

            if ($contractedTreatment->payment_type === 'abono') {
                $remainingBalance = $contractedTreatment->remainingBalance();
                if ($validated['total'] > $remainingBalance) {
                    return back()->with('error', 'El monto a registrar no puede exceder el saldo restante ($' . number_format($remainingBalance, 0, ',', '.') . ')')->withInput();
                }
            }

            $validated['branch_id'] = session('selected_branch_id') ?: ($contractedTreatment->branch_id ?? 1);

            $order = TreatmentOrder::create([
                'user_id' => $validated['user_id'],
                'branch_id' => $validated['branch_id'],
                'contracted_treatment_id' => $validated['contracted_treatment_id'],
                'total' => $validated['total'],
                'payment_method' => $validated['payment_method'],
                'status' => $validated['status'],
                'payment_status' => $validated['payment_status'],
                'paid_installments_ids' => $validated['paid_installments_ids'] ?? []
            ]);

            // Update installments if they were selected
            if (!empty($validated['paid_installments_ids'])) {
                $contractedTreatment->installments()
                    ->whereIn('id', $validated['paid_installments_ids'])
                    ->update([
                        'status' => 'PAID',
                        'paid_at' => now()
                    ]);
            }

            // Check if treatment status should transition to Paid
            if ($contractedTreatment->isFullyPaid()) {
                $contractedTreatment->update(['status' => 'Paid']);
            }

            // Process referral reward if applicable
            ReferralService::processReward($order->user);

            // Process repurchase commission (if applicable)
            RepurchaseService::processSale($order->user, $order);

            // Register income in accounting
            AccountingRecord::create([
                'branch_id' => $validated['branch_id'],
                'user_id' => $validated['user_id'],
                'type' => 'income',
                'amount' => $validated['total'],
                'description' => 'Pago manual de tratamiento: ' . ($contractedTreatment->treatment->name ?? 'N/A') . ' - Paciente: ' . ($order->user->name ?? 'N/A'),
                'category' => 'Tratamientos',
                'reference_id' => $order->id,
                'reference_type' => TreatmentOrder::class,
            ]);

            DB::commit();
            return redirect()->route('admin.payments.index')->with('success', 'Pago registrado exitosamente. Las cuotas/abonos han sido actualizados.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al registrar el pago: ' . $e->getMessage())->withInput();
        }
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
            }

            if ($order->contractedTreatment && $order->contractedTreatment->isFullyPaid()) {
                $order->contractedTreatment->update(['status' => 'Paid']);
            }

            // Process referral reward if applicable
            ReferralService::processReward($order->user);

            // Process repurchase commission (if applicable)
            RepurchaseService::processSale($order->user, $order);

            // Register income in accounting
            AccountingRecord::create([
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
     * Approve a pending product payment.
     */
    public function approveProduct(Order $order): RedirectResponse
    {
        if (in_array($order->status, ['Pago completado', 'Pagado', 'Entregado'])) {
            return back()->with('info', 'Esta orden ya fue aprobada.');
        }

        DB::beginTransaction();
        try {
            $order->update([
                'status' => 'Pago completado',
                'payment_status' => 'APPROVED',
            ]);

            \App\Models\AccountingRecord::create([
                'branch_id' => $order->branch_id ?? session('selected_branch_id') ?? 1,
                'user_id' => $order->user_id,
                'type' => 'income',
                'amount' => $order->total,
                'description' => 'Pago de productos aprobado - Paciente: ' . ($order->user->name ?? 'N/A'),
                'category' => 'Productos',
                'reference_id' => $order->id,
                'reference_type' => Order::class,
            ]);

            DB::commit();
            return back()->with('success', 'Pago de producto aprobado correctamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al aprobar el pago del producto: ' . $e->getMessage());
        }
    }

    /**
     * Reject a pending product payment.
     */
    public function rejectProduct(Request $request, Order $order): RedirectResponse
    {
        $request->validate(['reason' => 'nullable|string|max:255']);

        if (!in_array($order->status, ['Pago por verificar', 'Pendiente', 'Pending'])) {
            return back()->with('error', 'No se puede rechazar esta orden en su estado actual.');
        }

        $order->update([
            'status' => 'Cancelado',
            'payment_status' => 'DECLINED',
            'description' => $order->description . ' [Rechazado: ' . ($request->reason ?? 'Sin motivo') . ']'
        ]);

        return back()->with('success', 'El pago del producto ha sido rechazado.');
    }

    /**
     * Export payments.
     */
    public function export(): RedirectResponse
    {
        return back()->with('success', 'Exportación en construcción');
    }
}

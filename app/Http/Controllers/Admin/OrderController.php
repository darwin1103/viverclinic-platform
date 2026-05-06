<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrderController extends Controller
{
    // Definimos los estados permitidos
    const STATUSES = [
        'Pago por verificar',
        'Pago completado',
        'Entregado',
        'Cancelado'
    ];

    public function index(Request $request)
    {
        $query = Order::with(['user', 'branch']);

        // 1. Filtro por Sucursal
        $activeBranch = $request->input('branch_id') ?: session('selected_branch_id');
        if (!empty($activeBranch)) {
            $query->where('branch_id', $activeBranch);
        }

        // 2. Filtro por Búsqueda (Nombre o Email del paciente)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // 3. Filtro por Fechas
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->latest()->paginate(10);

        $branches = Branch::all();

        if ($request->has('branch_id')) {
            if ($request->filled('branch_id') || session('selected_branch_id')) {
                session(['selected_branch_id' => $request->input('branch_id')]);
            } else {
                session()->forget('selected_branch_id');
            }
        }
        $selectedBranchID = session('selected_branch_id', '');

        if ($request->ajax()) {
            return view('admin.orders.partials.table', compact('orders', 'branches', 'selectedBranchID'))->render();
        }

        return view('admin.orders.index', compact('orders', 'branches', 'selectedBranchID'));
    }

    public function show(Order $order)
    {
        $order->load(['items', 'user', 'branch']);
        $statuses = self::STATUSES;

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', self::STATUSES),
        ]);

        $previousStatus = $order->status;

        $order->update([
            'status' => $request->status
        ]);

        // Register income when order is approved (status changes to 'Pago completado')
        if ($request->status === 'Pago completado' && $previousStatus !== 'Pago completado') {
            \App\Models\AccountingRecord::create([
                'branch_id' => $order->branch_id,
                'user_id' => $order->user_id,
                'type' => 'income',
                'amount' => $order->total,
                'description' => 'Pago de productos aprobado - Paciente: ' . ($order->user->name ?? 'N/A'),
                'category' => 'Productos',
                'reference_id' => $order->id,
                'reference_type' => Order::class,
            ]);
        }

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'El estado de la orden se ha actualizado correctamente.');
    }

    public function downloadReceipt(Order $order)
    {
        if (!$order->payment_receipt || !Storage::exists($order->payment_receipt)) {
            abort(404, 'Comprobante no encontrado.');
        }

        return Storage::response($order->payment_receipt);
    }

}

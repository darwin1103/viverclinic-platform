<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use Illuminate\Http\Request;

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

        // 1. Filtro por Sucursal (Header)
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
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

        if ($request->filled('branch_id')) {
            session(['selected_branch_id' => $request->input('branch_id')]);
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

        $order->update([
            'status' => $request->status
        ]);

        return redirect()->route('admin.orders.show', $order)
            ->with('success', 'El estado de la orden se ha actualizado correctamente.');
    }
}

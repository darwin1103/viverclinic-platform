<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ManualSaleController extends Controller
{
    // Vista Principal
    public function index(Request $request)
    {
        $branches = Branch::all();

        if ($request->filled('branch_id')) {
            session(['selected_branch_id' => $request->input('branch_id')]);
        }
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.manual-sales.index', compact('branches', 'selectedBranchID'));
    }

    // Endpoint AJAX para renderizar grid de productos
    public function products(Request $request)
    {
        $query = Product::where('stock', '>', 0);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->get();

        return view('admin.manual-sales.partials.product-grid', compact('products'))->render();
    }

    // Endpoint AJAX para buscar pacientes (JSON)
    public function patients(Request $request)
    {
        // Obtener IDs de usuarios que tienen un patientProfile asociado a esa sucursal
        $query = User::whereHas('patientProfile', function ($q) use ($request) {
            if ($request->filled('branch_id')) {
                $q->where('branch_id', $request->branch_id);
            }
        })->role('PATIENT'); // Asegurar que sea paciente usando Spatie Permissions

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ];
        });

        return response()->json($users);
    }

    // Guardar la Venta
    public function store(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'user_id' => 'required|exists:users,id',
            'payment_method' => 'required|in:Efectivo,Punto de Venta,Transferencia Bancaria',
            'items' => 'required|json',
        ], [
            // Mensajes para la Sucursal
            'branch_id.required' => 'La sucursal es obligatoria para realizar la venta.',
            'branch_id.exists' => 'La sucursal seleccionada no es válida.',

            // Mensajes para el Paciente (User)
            'user_id.required' => 'Debe seleccionar un paciente.',
            'user_id.exists' => 'El paciente seleccionado no se encuentra registrado en el sistema.',

            // Mensajes para el Método de Pago
            'payment_method.required' => 'Por favor, seleccione un método de pago.',
            'payment_method.in' => 'El método de pago seleccionado no es válido.',

            // Mensajes para los Ítems (Carrito)
            'items.required' => 'El carrito de compras no puede estar vacío.',
            'items.json' => 'Ocurrió un error con el formato de los productos enviados.',
        ]);

        $itemsData = json_decode($request->items, true);

        if (empty($itemsData)) {
            return back()->with('error', 'El carrito está vacío.');
        }

        // Validar que el paciente pertenezca a la sucursal
        $patient = User::with('patientProfile')->findOrFail($request->user_id);
        if ($patient->patientProfile->branch_id != $request->branch_id) {
            return back()->with('error', 'El paciente no pertenece a la sucursal seleccionada.');
        }

        DB::beginTransaction();

        try {
            // 1. Crear Orden
            $order = Order::create([
                'user_id' => $patient->id,
                'branch_id' => $request->branch_id,
                'total' => 0, // Se calcula abajo
                'status' => 'Pago completado',
                'payment_method' => $request->payment_method,
                'payment_status' => 'APPROVED',
                'customer_email' => $patient->email,
                'currency' => 'COP',
                'description' => 'Venta en mostrador (Admin)',
            ]);

            $total = 0;

            foreach ($itemsData as $item) {
                $product = Product::where('branch_id', $request->branch_id)
                    ->where('id', $item['id'])
                    ->lockForUpdate()
                    ->first();

                if (!$product) {
                    throw new \Exception("El producto ID {$item['id']} no pertenece a esta sucursal o no existe.");
                }

                if ($product->stock < $item['qty']) {
                    throw new \Exception("Stock insuficiente para: " . $product->name);
                }

                $subtotal = $product->price * $item['qty'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['qty'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $item['qty']);
                $total += $subtotal;
            }

            $order->update(['total' => $total]);

            DB::commit();

            // Enviar Email
            Mail::to($patient->email)->queue(new OrderConfirmation($order));

            return redirect()->route('admin.orders.show',['order' => $order->id])
                ->with('success', 'Venta registrada exitosamente. Orden #' . $order->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage());
        }
    }
}

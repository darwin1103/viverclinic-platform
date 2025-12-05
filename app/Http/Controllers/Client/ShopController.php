<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Mail\OrderConfirmation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ShopController extends Controller
{
    // Mostrar Productos (Grid)
    public function index()
    {
        $user = Auth::user();
        // Asumiendo la relación: User -> PatientProfile -> Branch
        $branchId = $user->patientProfile->branch->id;

        $products = Product::where('branch_id', $branchId)
            ->where('stock', '>', 0)
            ->latest()
            ->get();

        return view('client.shop.index', compact('products'));
    }

    // Vista de Checkout (Recibe el form del index con cantidades)
    public function checkout(Request $request)
    {
        $quantities = $request->input('quantities', []);

        // Filtrar productos seleccionados (cantidad > 0)
        $selectedItems = [];
        $total = 0;

        foreach ($quantities as $productId => $qty) {
            $qty = intval($qty);
            if ($qty > 0) {
                $product = Product::find($productId);

                // Validación básica de stock backend antes de mostrar checkout
                if ($product && $product->stock >= $qty) {
                    $subtotal = $product->price * $qty;
                    $total += $subtotal;

                    $selectedItems[] = [
                        'product' => $product,
                        'quantity' => $qty,
                        'subtotal' => $subtotal
                    ];
                }
            }
        }

        if (empty($selectedItems)) {
            return redirect()->route('client.shop.index')->with('error', 'Debes seleccionar al menos un producto.');
        }

        return view('client.shop.checkout', compact('selectedItems', 'total'));
    }

    // Procesar la Compra
    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->patientProfile->branch->id;

        // Re-validar datos que vienen del form oculto o sesión
        // En este ejemplo simple, recibimos arrays de IDs y cantidades del form de checkout
        $items = json_decode($request->input('order_items'), true);

        if (!$items) {
            return redirect()->route('client.shop.index');
        }

        DB::beginTransaction();

        try {
            // 1. Crear Orden
            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'total' => 0, // Se calcula abajo
                'status' => 'Pago completado', // Simulado pago exitoso inmediato
                'payment_method' => 'Wompi - simulado', // Hardcodeado por ahora
                'payment_status' => 'Pago completado',
                'customer_email' => $user->email,
                'currency' => 'COP',
                'description' => 'Compra de productos médicos',
            ]);

            $orderTotal = 0;

            foreach ($items as $itemData) {
                // Bloqueo pesimista para evitar race conditions en stock
                $product = Product::where('id', $itemData['product_id'])->lockForUpdate()->first();

                if (!$product || $product->stock < $itemData['quantity']) {
                    throw new \Exception("El producto {$product->name} no tiene suficiente stock.");
                }

                // 2. Crear Item
                $subtotal = $product->price * $itemData['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                // 3. Descontar Stock
                $product->decrement('stock', $itemData['quantity']);
                $orderTotal += $subtotal;
            }

            // Actualizar total de la orden
            $order->update(['total' => $orderTotal]);

            DB::commit();

            // 4. Enviar Email (Queue)
            Mail::to($user->email)->queue(new OrderConfirmation($order));

            return redirect()->route('client.orders.index')->with('success', '¡Compra realizada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('client.shop.index')->with('error', 'Error en la compra: ' . $e->getMessage());
        }
    }
}

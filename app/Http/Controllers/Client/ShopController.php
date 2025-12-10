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
use Illuminate\Validation\Rule;

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

        // 1. Validaciones
        $request->validate([
            'payment_method' => ['required', 'in:GATEWAY,CASH,TRANSFER'],
            'payment_receipt' => [
                'nullable',
                Rule::requiredIf($request->payment_method === 'TRANSFER'),
                'image',
                'max:2048'
            ],
            'order_items' => 'required|json'
        ], [
            'payment_receipt.required' => 'Debes subir el comprobante para validar la transferencia.',
            'payment_receipt.image' => 'El archivo debe ser una imagen.',
        ]);

        $items = json_decode($request->input('order_items'), true);

        if (!$items) {
            return redirect()->route('client.shop.index')->with('error', 'Error en el carrito.');
        }

        DB::beginTransaction();

        try {
            // 2. Definir Estados y Datos según Método
            $status = 'Pago completado'; // Default para GATEWAY
            $paymentMethodLabel = 'Pasarela de Pagos';
            $receiptPath = null;

            if ($request->payment_method === 'CASH') {
                $status = 'Pago por verificar'; // O 'Pendiente de Pago'
                $paymentMethodLabel = 'Efectivo / Contra Entrega';
            }
            elseif ($request->payment_method === 'TRANSFER') {
                $status = 'Pago por verificar';
                $paymentMethodLabel = 'Transferencia Bancaria';

                // Subir archivo
                if ($request->hasFile('payment_receipt')) {
                    $receiptPath = $request->file('payment_receipt')->store('payment-receipts');
                }
            }

            // 3. Crear Orden
            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $branchId,
                'total' => 0,
                'status' => $status,
                'payment_method' => $paymentMethodLabel,
                'payment_status' => ($status === 'Pago completado') ? 'APPROVED' : 'PENDING',
                'payment_receipt' => $receiptPath, // Guardamos la ruta del archivo
                'customer_email' => $user->email,
                'currency' => 'COP',
                'description' => 'Compra Web - ' . $paymentMethodLabel,
            ]);

            $orderTotal = 0;

            foreach ($items as $itemData) {
                $product = Product::where('id', $itemData['product_id'])->lockForUpdate()->first();

                if (!$product || $product->stock < $itemData['quantity']) {
                    throw new \Exception("El producto {$product->name} no tiene suficiente stock.");
                }

                $subtotal = $product->price * $itemData['quantity'];

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $subtotal,
                ]);

                $product->decrement('stock', $itemData['quantity']);
                $orderTotal += $subtotal;
            }

            $order->update(['total' => $orderTotal]);

            DB::commit();

            Mail::to($user->email)->queue(new OrderConfirmation($order));

            return redirect()->route('client.orders.index')->with('success', '¡Orden registrada con éxito!');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('client.shop.index')->with('error', 'Error en la compra: ' . $e->getMessage());
        }
    }
}

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
use App\Models\Setting;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Http;

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

        $selectedItems = [];
        $total = 0;

        foreach ($quantities as $productId => $qty) {
            $qty = intval($qty);
            if ($qty > 0) {
                $product = Product::find($productId);
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
            return redirect()->route('client.shop.index')->with('error', 'Selecciona al menos un producto.');
        }

        // --- Lógica Wompi ---
        $wompiPublicKey = Setting::get('wompi_public_key');
        $wompiSecret = Setting::get('wompi_integrity_secret');
        $wompiData = null;

        if ($wompiPublicKey && $wompiSecret) {
            // 1. Generar Referencia Única
            $reference = 'ORD-' . time() . '-' . Str::random(4);

            // 2. Calcular Monto en Centavos
            $amountInCents = $total * 100;
            $currency = 'COP';

            // 3. Generar Firma de Integridad (SHA256)
            // Concatenación: Referencia + MontoEnCentavos + Moneda + Secreto
            $signatureString = $reference . $amountInCents . $currency . $wompiSecret;
            $integritySignature = hash('sha256', $signatureString);

            $redirectUrl = (config('app.env') == 'production') ? route('client.payment.result') : 'https://dev.viverclinic.com/payment/result'; // usar url real ***

            $wompiData = [
                'public_key' => $wompiPublicKey,
                'currency' => $currency,
                'amount_in_cents' => $amountInCents,
                'reference' => $reference,
                'signature' => $integritySignature,
                'redirect_url' => $redirectUrl, // usar url real ***
            ];

            // Guardamos los items y la referencia en sesión para recuperarlos al volver de Wompi
            Session::put('checkout_pending_order', [
                'reference' => $reference,
                'items' => $selectedItems,
                'total' => $total
            ]);
        }

        return view('client.shop.checkout', compact('selectedItems', 'total', 'wompiData'));
    }

    // Procesar la Compra
    public function placeOrder(Request $request)
    {
        $user = Auth::user();
        $branchId = $user->patientProfile->branch->id;

        // 1. Validaciones
        $request->validate([
            'payment_method' => ['required', 'in:CASH,TRANSFER'],
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


    // Método wompiResult
    public function wompiResult(Request $request)
    {
        // Wompi envía el ID de la transacción en la URL: ?id=xxxx
        $transactionId = $request->query('id');

        if (!$transactionId) {
            return redirect()->route('client.shop.index')->with('error', 'No se recibió información del pago.');
        }

        // 1. Verificar transacción con la API de Wompi (Recomendado para seguridad)
        // OJO: Usar 'production' o 'sandbox' según tus llaves. Wompi tiene URLs diferentes.
        // Detectamos si es prod por el prefijo de la llave pública guardada
        $pubKey = Setting::get('wompi_public_key');
        $isProd = str_starts_with($pubKey, 'pub_prod_');
        $url = $isProd ? "https://production.wompi.co/v1/transactions/{$transactionId}"
                       : "https://sandbox.wompi.co/v1/transactions/{$transactionId}";

        try {
            $response = Http::get($url);
            $data = $response->json();

            if (!isset($data['data'])) {
                throw new \Exception('Respuesta inválida de Wompi');
            }

            $transaction = $data['data'];
            $status = $transaction['status']; // APPROVED, DECLINED, VOIDED, ERROR
            $reference = $transaction['reference'];

            // 2. Recuperar datos del carrito de la sesión
            $sessionData = Session::get('checkout_pending_order');

            // Validar que la referencia coincida para evitar secuestros de sesión
            if (!$sessionData || $sessionData['reference'] !== $reference) {
                // Si no hay sesión (ej. pagó en otra pestaña o expiró), intentamos buscar si la orden ya existe
                // Si no existe, es un problema porque perdimos qué productos compró.
                // Para este ejercicio asumiremos que la sesión vive lo suficiente.
                return redirect()->route('client.shop.index')->with('error', 'La sesión de compra ha expirado o la referencia no coincide.');
            }

            // 3. Crear la Orden si fue aprobada (o incluso si fue rechazada para historial)
            // Solo creamos si es APPROVED para este flujo simple, o guardamos con estado

            $orderStatus = match($status) {
                'APPROVED' => 'Pago completado',
                'DECLINED' => 'Cancelado',
                'ERROR' => 'Cancelado',
                default => 'Pago por verificar'
            };

            // Evitar duplicados: Verificar si ya existe orden con esa referencia
            $existingOrder = Order::where('payment_reference', $transactionId)->first();
            if ($existingOrder) {
                return view('client.shop.thank-you', ['order' => $existingOrder]);
            }

            // Crear Orden
            DB::beginTransaction();

            $user = Auth::user();
            $order = Order::create([
                'user_id' => $user->id,
                'branch_id' => $user->patientProfile->branch->id,
                'total' => $sessionData['total'],
                'status' => $orderStatus,
                'payment_method' => 'Wompi (' . $transaction['payment_method_type'] . ')',
                'payment_status' => $status,
                'payment_reference' => $transactionId, // Guardamos el ID de Wompi
                'customer_email' => $transaction['customer_email'] ?? null,
                'currency' => 'COP',
                'description' => 'Compra Online Wompi Ref: ' . $reference,
            ]);

            foreach ($sessionData['items'] as $item) {
                // Re-verificar producto
                $product = Product::find($item['product']->id);
                // (Opcional: Volver a chequear stock, aunque ya se "apartó" visualmente)

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'subtotal' => $item['subtotal'],
                ]);

                // Descontar Stock si fue aprobado
                if ($status === 'APPROVED') {
                    $product->decrement('stock', $item['quantity']);
                }
            }

            DB::commit();

            // Limpiar sesión
            Session::forget('checkout_pending_order');

            // Enviar Email
            if ($status === 'APPROVED') {
                Mail::to($user->email)->queue(new OrderConfirmation($order));
            }

            return view('client.shop.thank-you', compact('order'));

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('client.shop.index')->with('error', 'Error verificando el pago: ' . $e->getMessage());
        }
    }

}

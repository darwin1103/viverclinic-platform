<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use App\Models\TreatmentOrder;
use App\Models\Setting;
use App\Services\ReferralService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\TreatmentOrderConfirmation; // Asegúrate de crear este Mailable

class TreatmentPaymentController extends Controller
{
    /**
     * Procesa el formulario de pago (Efectivo, Transferencia o Prepara Wompi)
     */
    public function process(Request $request)
    {
        $request->validate([
            'contracted_treatment_id' => 'required|exists:contracted_treatments,id',
            'payment_type' => 'required|in:full,installment', // Qué paga (Cuota vs Total)
            'payment_method' => 'required|in:CASH,TRANSFER,WOMPI', // Cómo paga
            'payment_receipt' => 'nullable|required_if:payment_method,TRANSFER|image|max:4096',
        ]);

        $user = Auth::user();
        $contractedTreatment = ContractedTreatment::findOrFail($request->contracted_treatment_id);

        if ($contractedTreatment->user_id !== $user->id) abort(403);

        DB::beginTransaction();
        try {
            // 1. Calcular Monto y Definir qué cuotas se pagan
            $amount = 0;
            $description = '';
            $targetInstallments = []; // Colección de cuotas a afectar

            if ($request->payment_type === 'installment' && $contractedTreatment->hasInstallments()) {
                // Pagar siguiente cuota pendiente
                $installment = $contractedTreatment->installments()
                    ->where('status', 'PENDING')
                    ->orderBy('installment_number')
                    ->first();

                if (!$installment) return back()->with('error', 'No hay cuotas pendientes.');

                $amount = $installment->price;
                $description = "Pago Cuota #{$installment->installment_number} - {$contractedTreatment->treatment->name}";
                $targetInstallments[] = $installment;

            } else {
                // Pago Total Restante
                if ($contractedTreatment->hasInstallments()) {
                    $pending = $contractedTreatment->installments()->where('status', 'PENDING')->get();
                    $amount = $pending->sum('price');
                    $targetInstallments = $pending;
                } else {
                    $amount = $contractedTreatment->total_price;
                }
                $description = "Pago Total Restante - {$contractedTreatment->treatment->name}";
            }

            // 2. Manejo de Wompi (Redirección a vista intermedia con Widget)
            if ($request->payment_method === 'WOMPI') {
                DB::commit(); // No guardamos nada en BD todavía, solo calculamos

                return $this->renderWompiView(
                    $amount,
                    $description,
                    $user,
                    $contractedTreatment,
                    $request->payment_type,
                    collect($targetInstallments)->pluck('id')->toArray()
                );
            }

            // 3. Manejo Manual (Efectivo / Transferencia)
            $orderStatus = 'Pago por verificar';
            $paymentStatus = 'PENDING';
            $receiptPath = null;

            if ($request->hasFile('payment_receipt')) {
                $receiptPath = $request->file('payment_receipt')->store('treatment_receipts', 'public');
            }

            // Crear la Orden
            $order = TreatmentOrder::create([
                'user_id' => $user->id,
                'branch_id' => $contractedTreatment->branch_id,
                'contracted_treatment_id' => $contractedTreatment->id,
                'total' => $amount,
                'status' => $orderStatus,
                'payment_method' => $request->payment_method === 'CASH' ? 'Efectivo' : 'Transferencia',
                'payment_status' => $paymentStatus,
                'payment_description' => $description,
                'payment_receipt' => $receiptPath,
                'currency' => 'COP',
                'customer_email' => $user->email,
                // Guardamos qué cuotas intentó pagar para procesarlas cuando se apruebe el pago
                'paid_installments_ids' => collect($targetInstallments)->pluck('id')->toArray()
            ]);

            DB::commit();

            // Enviar correo de pendiente
            Mail::to($user)->queue(new TreatmentOrderConfirmation($order));

            return redirect()->route('client.treatment.payment.thank-you', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error procesando el pago: ' . $e->getMessage());
        }
    }

    /**
     * Prepara y muestra la vista con el Widget de Wompi
     */
    private function renderWompiView($amount, $description, $user, $contractedTreatment, $paymentType, $installmentIds)
    {
        $pubKey = Setting::get('wompi_public_key');
        $secret = Setting::get('wompi_integrity_secret');

        if (!$pubKey || !$secret) {
            return back()->with('error', 'La pasarela de pagos no está configurada.');
        }

        $reference = 'TRT-' . time() . '-' . Str::random(4);
        $amountInCents = $amount * 100;
        $currency = 'COP';

        // Firma de integridad
        $signatureString = $reference . $amountInCents . $currency . $secret;
        $integritySignature = hash('sha256', $signatureString);

        $redirectUrl = route('client.treatment.payment.result');

        // Datos para Wompi
        $wompiData = [
            'public_key' => $pubKey,
            'currency' => $currency,
            'amount_in_cents' => $amountInCents,
            'reference' => $reference,
            'signature' => $integritySignature,
            'redirect_url' => $redirectUrl,
            'email' => $user->email,
            'full_name' => $user->name,
        ];

        // Guardamos en sesión los datos contextuales para recuperarlos al volver de Wompi
        session()->put('wompi_pending_treatment_payment', [
            'contracted_treatment_id' => $contractedTreatment->id,
            'payment_type' => $paymentType,
            'installment_ids' => $installmentIds,
            'description' => $description,
            'amount' => $amount,
            'reference' => $reference
        ]);

        return view('client.payment.wompi-checkout', compact('wompiData', 'amount', 'description'));
    }

    /**
     * Retorno de Wompi
     */
    public function wompiResult(Request $request)
    {
        $transactionId = $request->query('id');
        $sessionData = session()->get('wompi_pending_treatment_payment');

        if (!$transactionId || !$sessionData) {
            return redirect()->route('client.contracted-treatment.index')->with('error', 'Sesión de pago inválida.');
        }

        // Verificar transacción con API Wompi
        $pubKey = Setting::get('wompi_public_key');
        $isProd = str_starts_with($pubKey, 'pub_prod_');
        $url = $isProd ? "https://production.wompi.co/v1/transactions/{$transactionId}"
                       : "https://sandbox.wompi.co/v1/transactions/{$transactionId}";

        try {
            $response = Http::get($url);
            $data = $response->json()['data'] ?? null;

            if (!$data) throw new \Exception('Respuesta inválida de Wompi');

            $status = $data['status']; // APPROVED, DECLINED, ERROR

            // Crear Orden
            DB::beginTransaction();

            $orderStatus = ($status === 'APPROVED') ? 'Pago completado' : 'Cancelado';

            $contractedTreatment = ContractedTreatment::find($sessionData['contracted_treatment_id']);

            // Validar que no exista la orden ya
            $existing = TreatmentOrder::where('payment_reference', $transactionId)->first();
            if($existing) return redirect()->route('client.treatment.payment.thank-you', $existing);

            $order = TreatmentOrder::create([
                'user_id' => Auth::id(),
                'branch_id' => $contractedTreatment->branch_id,
                'contracted_treatment_id' => $contractedTreatment->id,
                'total' => $sessionData['amount'],
                'status' => $orderStatus,
                'payment_method' => 'Wompi (' . ($data['payment_method_type'] ?? 'N/A') . ')',
                'payment_status' => $status,
                'payment_reference' => $transactionId,
                'currency' => 'COP',
                'customer_email' => $data['customer_email'] ?? Auth::user()->email,
                'payment_description' => $sessionData['description'],
                'paid_installments_ids' => $sessionData['installment_ids']
            ]);

            if ($status === 'APPROVED') {
                // Actualizar Cuotas
                if (!empty($sessionData['installment_ids'])) {
                     // Actualizar cuotas especificas
                     $contractedTreatment->installments()
                        ->whereIn('id', $sessionData['installment_ids'])
                        ->update(['status' => 'PAID', 'paid_at' => now()]);
                } else if ($sessionData['payment_type'] === 'full') {
                     // Si era full y no tenía cuotas, o falló el array
                     if($contractedTreatment->hasInstallments()){
                         $contractedTreatment->installments()->update(['status' => 'PAID', 'paid_at' => now()]);
                     }
                }

                // Verificar si se completó todo el contrato
                $pendingCount = $contractedTreatment->installments()->where('status', 'PENDING')->count();
                if ($pendingCount === 0) {
                    $contractedTreatment->update(['status' => 'Paid']);
                }

                // Procesar recompensa de referido (si aplica)
                ReferralService::processReward(Auth::user());
            }

            DB::commit();
            session()->forget('wompi_pending_treatment_payment');

            Mail::to(Auth::user())->queue(new TreatmentOrderConfirmation($order));

            return redirect()->route('client.treatment.payment.thank-you', $order);

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('client.contracted-treatment.index')->with('error', 'Error verificando Wompi: ' . $e->getMessage());
        }
    }

    public function thankYou(TreatmentOrder $order)
    {
        if ($order->user_id !== Auth::id()) abort(403);
        return view('client.payment.thank-you', compact('order'));
    }
}

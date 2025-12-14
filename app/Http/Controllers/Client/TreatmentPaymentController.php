<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use App\Models\TreatmentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TreatmentPaymentController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'contracted_treatment_id' => 'required|exists:contracted_treatments,id',
            'payment_type' => 'required|in:full,installment'
        ]);

        $user = Auth::user();
        $contractedTreatment = ContractedTreatment::findOrFail($request->contracted_treatment_id);

        if ($contractedTreatment->user_id !== $user->id) {
            abort(403);
        }

        try {
            DB::beginTransaction();

            $amount = 0;
            $description = '';
            $paidInstallmentIds = [];
            $newStatus = 'PENDING'; // Status de la orden

            if ($request->payment_type === 'installment' && $contractedTreatment->hasInstallments()) {
                // Pagar la siguiente cuota pendiente
                $installment = $contractedTreatment->installments()
                    ->where('status', 'PENDING')
                    ->orderBy('installment_number')
                    ->first();

                if (!$installment) {
                    return response()->json(['message' => 'No hay cuotas pendientes.'], 400);
                }

                $amount = $installment->price;
                $description = "Pago Cuota #{$installment->installment_number}";

                // Actualizar cuota
                $installment->update([
                    'status' => 'PAID',
                    'paid_at' => now()
                ]);
                $paidInstallmentIds[] = $installment->id;

                // Verificar si quedan cuotas pendientes para actualizar el contrato principal
                if ($contractedTreatment->installments()->where('status', 'PENDING')->count() === 0) {
                     $contractedTreatment->update(['status' => 'Paid']);
                }

            } else {
                // Pago Total (o restante)
                // Si hay cuotas, sumamos las pendientes
                if ($contractedTreatment->hasInstallments()) {
                    $pendingInstallments = $contractedTreatment->installments()->where('status', 'PENDING')->get();
                    $amount = $pendingInstallments->sum('price');

                    foreach ($pendingInstallments as $inst) {
                        $inst->update(['status' => 'PAID', 'paid_at' => now()]);
                        $paidInstallmentIds[] = $inst->id;
                    }
                } else {
                    // Si no hay cuotas, es el total del contrato
                    $amount = $contractedTreatment->total_price;
                }

                $description = "Pago Total Restante";
                $contractedTreatment->update(['status' => 'Paid']);
            }

            // Crear la Orden
            TreatmentOrder::create([
                'user_id' => $user->id,
                'branch_id' => $contractedTreatment->branch_id,
                'contracted_treatment_id' => $contractedTreatment->id,
                'total' => $amount,
                'status' => 'PAID', // Simulamos pago exitoso directo
                'payment_method' => 'Credit Card (Mock)',
                'payment_description' => $description,
                'paid_installments_ids' => $paidInstallmentIds
            ]);

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pago realizado con éxito.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Error procesando el pago: ' . $e->getMessage()], 500);
        }
    }
}

<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ContractedTreatment;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\TreatmentOrder;
use App\Models\User;

class ReferralService
{
    /**
     * Procesar recompensa de referido cuando se completa el primer pago.
     * Se llama desde TreatmentPaymentController (Wompi) y ContractedTreatmentController (aprobación manual).
     */
    public static function processReward(User $paidUser): void
    {
        // Verificar que el sistema de referidos esté habilitado
        if (!Setting::get('referral_enabled', '1')) {
            return;
        }

        // 1. Buscar si el usuario fue referido y aún no se dio recompensa
        $referral = Referral::where('referred_id', $paidUser->id)
            ->where('status', 'registered')
            ->first();

        if (!$referral) {
            return;
        }

        // 2. Verificar que sea su primer pago completado (APPROVED)
        $approvedOrdersCount = TreatmentOrder::where('user_id', $paidUser->id)
            ->where('payment_status', 'APPROVED')
            ->count();

        if ($approvedOrdersCount !== 1) {
            return; // No es el primer pago o no hay pagos aprobados
        }

        // 3. Obtener configuración
        $bonusSessions = (int) Setting::get('referral_bonus_sessions', 3);

        $referrer = $referral->referrer;
        // The sessions are no longer automatically applied to an active treatment.
        // They remain in referral.bonus_sessions to be redeemed manually by the user.

        // 4. Marcar referido como recompensado
        $referral->status = 'rewarded';
        $referral->bonus_sessions = $bonusSessions;
        $referral->rewarded_at = now();
        $referral->save();

        // 5. Registrar la venta si está habilitado
        if (Setting::get('referral_sales_enabled', '1')) {
            $lastAttendedAppt = Appointment::whereHas('contractedTreatment', function ($q) use ($referrer) {
                    $q->where('user_id', $referrer->id);
                })
                ->where('attended', true)
                ->whereNotNull('staff_user_id')
                ->latest('schedule')
                ->first();

            $staffId = $lastAttendedAppt ? $lastAttendedAppt->staff_user_id : null;

            $firstOrder = TreatmentOrder::where('user_id', $paidUser->id)
                ->where('payment_status', 'APPROVED')
                ->first();

            if ($firstOrder) {
                \App\Models\Sale::create([
                    'branch_id' => $firstOrder->contractedTreatment->branch_id ?? session('branch_id'),
                    'staff_user_id' => $staffId,
                    'patient_user_id' => $paidUser->id,
                    'contracted_treatment_id' => $firstOrder->contracted_treatment_id,
                    'type' => 'referral',
                    'first_payment_amount' => $firstOrder->total,
                ]);
            }
        }
    }
}

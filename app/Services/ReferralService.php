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
        $commissionType = Setting::get('referral_commission_type', 'fixed');
        $commissionValue = (float) Setting::get('referral_commission_value', 0);

        $referrer = $referral->referrer;
        // The sessions are no longer automatically applied to an active treatment.
        // They remain in referral.bonus_sessions to be redeemed manually by the user.

        // 5. Calcular comisión para la última empleada que atendió al referidor
        $lastAttendedAppt = Appointment::whereHas('contractedTreatment', function ($q) use ($referrer) {
                $q->where('user_id', $referrer->id);
            })
            ->where('attended', true)
            ->whereNotNull('staff_user_id')
            ->latest('schedule')
            ->first();

        $commissionAmount = 0;
        if ($lastAttendedAppt && $commissionValue > 0) {
            $staffId = $lastAttendedAppt->staff_user_id;

            if ($commissionType === 'percentage') {
                // Calcular porcentaje sobre el total de la primera orden del referido
                $orderTotal = TreatmentOrder::where('user_id', $paidUser->id)
                    ->where('payment_status', 'APPROVED')
                    ->first()->total ?? 0;
                $commissionAmount = round(($orderTotal * $commissionValue) / 100, 2);
            } else {
                $commissionAmount = $commissionValue;
            }

            $referral->staff_id = $staffId;
            $referral->staff_commission = $commissionAmount;
            $referral->staff_commission_status = 'pending';
        }

        // 6. Marcar referido como recompensado
        $referral->status = 'rewarded';
        $referral->bonus_sessions = $bonusSessions;
        $referral->rewarded_at = now();
        $referral->save();
    }

    /**
     * Aplicar sesiones pendientes de referidos al contratar un nuevo tratamiento.
     * Se puede llamar al crear un ContractedTreatment.
     */
    public static function applyPendingSessions(ContractedTreatment $contractedTreatment): void
    {
        // Deprecated: sessions are now redeemed manually via the patient dashboard.
    }
}

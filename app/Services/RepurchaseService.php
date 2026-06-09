<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ContractedTreatment;
use App\Models\RepurchaseCommission;
use App\Models\Setting;
use App\Models\TreatmentOrder;
use App\Models\User;

class RepurchaseService
{
    /**
     * Process repurchase commission when a returning patient completes their first payment on a new treatment.
     * Called from ContractedTreatmentController (manual approval) and TreatmentPaymentController (Wompi).
     */
    public static function processCommission(User $paidUser, TreatmentOrder $order): void
    {
        // 1. Check if user has more than 1 contracted treatment (repurchase)
        $totalContracts = ContractedTreatment::where('user_id', $paidUser->id)->count();
        if ($totalContracts <= 1) {
            return;
        }

        // 2. Get the contracted_treatment_id from the order
        $contractedTreatmentId = $order->contracted_treatment_id;
        if (!$contractedTreatmentId) {
            return;
        }

        // 3. Check this is the first approved payment for this specific contracted treatment
        $approvedPayments = TreatmentOrder::where('contracted_treatment_id', $contractedTreatmentId)
            ->where('payment_status', 'APPROVED')
            ->count();

        if ($approvedPayments !== 1) {
            return;
        }

        // 4. Check no RepurchaseCommission exists for this contracted_treatment_id already
        if (RepurchaseCommission::where('contracted_treatment_id', $contractedTreatmentId)->exists()) {
            return;
        }

        // 5. Get settings
        $commissionType = Setting::get('repurchase_commission_type', 'fixed');
        $commissionValue = (float) Setting::get('repurchase_commission_value', '0');

        // 6. If commission_value <= 0, return
        if ($commissionValue <= 0) {
            return;
        }

        // 7. Find the last attended appointment of the patient across all treatments
        $lastAttendedAppt = Appointment::whereHas('contractedTreatment', function ($q) use ($paidUser) {
                $q->where('user_id', $paidUser->id);
            })
            ->where(function($q) {
                $q->where('attended', true)
                  ->orWhereIn('status', ['Atendida', 'Completada']);
            })
            ->whereNotNull('staff_user_id')
            ->latest('schedule')
            ->first();

        // 8. If no staff found, return
        if (!$lastAttendedAppt) {
            return;
        }

        $staffId = $lastAttendedAppt->staff_user_id;

        // 9. Calculate commission amount
        $contractedTreatment = ContractedTreatment::find($contractedTreatmentId);
        $treatmentTotal = $contractedTreatment->total_price ?? 0;

        if ($commissionType === 'percentage') {
            $commissionAmount = round(($treatmentTotal * $commissionValue) / 100, 2);
        } else {
            $commissionAmount = $commissionValue;
        }

        // 10. Create RepurchaseCommission record
        RepurchaseCommission::create([
            'contracted_treatment_id' => $contractedTreatmentId,
            'branch_id' => $order->branch_id ?? $contractedTreatment->branch_id ?? session('selected_branch_id') ?? 1,
            'staff_user_id' => $staffId,
            'commission_amount' => $commissionAmount,
            'commission_type' => $commissionType,
            'commission_value' => $commissionValue,
            'treatment_total' => $treatmentTotal,
            'status' => 'approved',
        ]);
    }
}

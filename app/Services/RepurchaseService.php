<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\ContractedTreatment;
use App\Models\RepurchaseCommission;
use App\Models\Setting;
use App\Models\TreatmentOrder;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class RepurchaseService
{
    /**
     * Process repurchase commission when a returning patient completes their first payment on a new treatment.
     * Called from ContractedTreatmentController (manual approval) and TreatmentPaymentController (Wompi).
     */
    public static function processCommission(User $paidUser, TreatmentOrder $order): void
    {
        Log::info('[RepurchaseService] START processCommission', [
            'user_id' => $paidUser->id,
            'order_id' => $order->id,
            'order_payment_status' => $order->payment_status,
            'contracted_treatment_id' => $order->contracted_treatment_id,
        ]);

        // 1. Check if user has more than 1 contracted treatment (repurchase)
        $totalContracts = ContractedTreatment::where('user_id', $paidUser->id)->count();
        if ($totalContracts <= 1) {
            Log::info('[RepurchaseService] EXIT #1: Not a repurchase, totalContracts=' . $totalContracts);
            return;
        }

        // 2. Get the contracted_treatment_id from the order
        $contractedTreatmentId = $order->contracted_treatment_id;
        if (!$contractedTreatmentId) {
            Log::info('[RepurchaseService] EXIT #2: No contracted_treatment_id on order');
            return;
        }

        // 3. Check this is the first approved payment for this specific contracted treatment
        $approvedPayments = TreatmentOrder::where('contracted_treatment_id', $contractedTreatmentId)
            ->where('payment_status', 'APPROVED')
            ->count();

        Log::info('[RepurchaseService] Step 3: approvedPayments count=' . $approvedPayments . ' for contract=' . $contractedTreatmentId);

        if ($approvedPayments !== 1) {
            Log::info('[RepurchaseService] EXIT #3: approvedPayments is not 1, it is ' . $approvedPayments);
            return;
        }

        // 4. Check no RepurchaseCommission exists for this contracted_treatment_id already
        if (RepurchaseCommission::where('contracted_treatment_id', $contractedTreatmentId)->exists()) {
            Log::info('[RepurchaseService] EXIT #4: Commission already exists for contract=' . $contractedTreatmentId);
            return;
        }

        // 5. Get settings
        $commissionType = Setting::get('repurchase_commission_type', 'fixed');
        $commissionValue = (float) Setting::get('repurchase_commission_value', '0');

        Log::info('[RepurchaseService] Step 5: commissionType=' . $commissionType . ', commissionValue=' . $commissionValue);

        // 6. If commission_value <= 0, return
        if ($commissionValue <= 0) {
            Log::info('[RepurchaseService] EXIT #5: Commission value is 0 or not configured');
            return;
        }

        // 7. Find the last attended appointment of the patient across all treatments
        $lastAttendedAppt = Appointment::whereHas('contractedTreatment', function ($q) use ($paidUser) {
                $q->where('user_id', $paidUser->id);
            })
            ->whereNotNull('staff_user_id')
            ->latest('schedule')
            ->first();

        // 8. If no staff found, return
        if (!$lastAttendedAppt) {
            Log::info('[RepurchaseService] EXIT #6: No appointment with staff found for user=' . $paidUser->id);
            return;
        }

        $staffId = $lastAttendedAppt->staff_user_id;
        Log::info('[RepurchaseService] Step 8: Found staff_user_id=' . $staffId . ' from appointment=' . $lastAttendedAppt->id);

        // 9. Calculate commission amount based on the first payment amount
        $contractedTreatment = ContractedTreatment::find($contractedTreatmentId);
        $paymentAmount = $order->total ?? 0;

        if ($commissionType === 'percentage') {
            $commissionAmount = round(($paymentAmount * $commissionValue) / 100, 2);
        } else {
            $commissionAmount = $commissionValue;
        }

        Log::info('[RepurchaseService] Step 9: commissionAmount=' . $commissionAmount . ', paymentAmount=' . $paymentAmount);

        // 10. Create RepurchaseCommission record
        $commission = RepurchaseCommission::create([
            'contracted_treatment_id' => $contractedTreatmentId,
            'branch_id' => $order->branch_id ?? $contractedTreatment->branch_id ?? session('selected_branch_id') ?? 1,
            'staff_user_id' => $staffId,
            'commission_amount' => $commissionAmount,
            'commission_type' => $commissionType,
            'commission_value' => $commissionValue,
            'treatment_total' => $paymentAmount,
            'status' => 'approved',
        ]);

        Log::info('[RepurchaseService] SUCCESS: RepurchaseCommission created', ['id' => $commission->id]);
    }
}

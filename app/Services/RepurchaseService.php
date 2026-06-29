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
     * Process repurchase sale when a returning patient completes their first payment on a new treatment.
     * Called from ContractedTreatmentController (manual approval) and TreatmentPaymentController (Wompi).
     */
    public static function processSale(User $paidUser, TreatmentOrder $order): void
    {
        Log::info('[RepurchaseService] START processSale', [
            'user_id' => $paidUser->id,
            'order_id' => $order->id,
            'order_payment_status' => $order->payment_status,
            'contracted_treatment_id' => $order->contracted_treatment_id,
        ]);

        if (!Setting::get('repurchase_sales_enabled', '1')) {
            Log::info('[RepurchaseService] EXIT #0: Repurchase sales tracking disabled');
            return;
        }

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

        // 4. Check no Sale exists for this contracted_treatment_id already
        if (\App\Models\Sale::where('contracted_treatment_id', $contractedTreatmentId)->where('type', 'repurchase')->exists()) {
            Log::info('[RepurchaseService] EXIT #4: Sale already exists for contract=' . $contractedTreatmentId);
            return;
        }

        // 7. Find the last attended appointment of the patient across all treatments
        $lastAttendedAppt = Appointment::whereHas('contractedTreatment', function ($q) use ($paidUser) {
                $q->where('user_id', $paidUser->id);
            })
            ->whereNotNull('staff_user_id')
            ->latest('schedule')
            ->first();

        // 8. If no staff found, allow null staff for manual assignment later or just set null
        $staffId = $lastAttendedAppt ? $lastAttendedAppt->staff_user_id : null;
        Log::info('[RepurchaseService] Step 8: Found staff_user_id=' . $staffId);

        $contractedTreatment = ContractedTreatment::find($contractedTreatmentId);
        $paymentAmount = $order->total ?? 0;

        // 10. Create Sale record
        $sale = \App\Models\Sale::create([
            'branch_id' => $order->branch_id ?? $contractedTreatment->branch_id ?? session('selected_branch_id') ?? 1,
            'staff_user_id' => $staffId,
            'patient_user_id' => $paidUser->id,
            'contracted_treatment_id' => $contractedTreatmentId,
            'type' => 'repurchase',
            'first_payment_amount' => $paymentAmount,
        ]);

        Log::info('[RepurchaseService] SUCCESS: Sale created', ['id' => $sale->id]);
    }
}

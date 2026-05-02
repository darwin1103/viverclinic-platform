<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TreatmentOrder;

class PaymentController extends Controller
{
    /**
     * Display a listing of the payments.
     */
    public function index(): View
    {
        $branchId = auth()->user()->staffProfile->branch_id ?? null;
        
        $payments = TreatmentOrder::with(['user', 'contractedTreatment.treatment'])
            ->where('branch_id', $branchId)
            ->latest()
            ->get();
            
        return view('staff.payments.index', compact('payments'));
    }
}

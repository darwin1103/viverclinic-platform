<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractedTreatmentController extends Controller
{

    public function index(Request $request)
    {

        $user = Auth::user();

        $query = ContractedTreatment::with(['user', 'treatment'])
            ->where('user_id', $user->id)
            ->latest(); // Ordenar por más reciente

        $contractedTreatments = $query->paginate(15);

        return view('client.contracted-treatment.index', compact('contractedTreatments'));
    }

    public function show(ContractedTreatment $contractedTreatment)
    {
        $user = Auth::user();
        if ($contractedTreatment->user_id !== $user->id) {
            abort(403);
        }

        $contractedTreatment->load(['user', 'branch', 'treatment', 'installments', 'orders']);

        $paymentVerificationPending = $contractedTreatment->orders()
            ->where('status', 'Pago por verificar')
            ->exists();

        $lastOrder = $contractedTreatment->orders()->latest()->first();
        $lastOrderRejected = ($lastOrder && $lastOrder->status === 'Cancelado');
        $lastOrderMessage = $lastOrder ? $lastOrder->payment_description : '';

        $minimumAbonoAmount = (int) \App\Models\Setting::get('minimum_abono_amount', '50000');

        return view('client.contracted-treatment.show', compact(
            'contractedTreatment',
            'paymentVerificationPending',
            'lastOrderRejected',
            'lastOrderMessage',
            'minimumAbonoAmount'
        ));
    }


}

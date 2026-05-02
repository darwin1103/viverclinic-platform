<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

use App\Models\TreatmentOrder;
use App\Models\User;
use App\Models\ContractedTreatment;

class PaymentController extends Controller
{
    /**
     * Display payments index list.
     */
    public function index(): View
    {
        $payments = TreatmentOrder::with(['user', 'contractedTreatment.treatment'])->latest()->get();
        return view('admin.payments.index', compact('payments'));
    }

    /**
     * Display pending payments list.
     */
    public function pending(): View
    {
        $pendingPayments = TreatmentOrder::with(['user', 'contractedTreatment.treatment'])
            ->whereIn('status', ['Pending', 'Pendiente', 'Pago por verificar'])
            ->latest()
            ->get();
            
        return view('admin.payments.pending', compact('pendingPayments'));
    }

    /**
     * Show the form for creating a new payment.
     */
    public function create(): View
    {
        $patients = User::role('PATIENT')->select(['id', 'name'])->get();
        $contractedTreatments = ContractedTreatment::with('treatment')
            ->whereIn('status', ['Activo', 'Pending', 'In Progress'])
            ->get();
            
        return view('admin.payments.create', compact('patients', 'contractedTreatments'));
    }

    /**
     * Store a newly created payment.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'contracted_treatment_id' => 'required|exists:contracted_treatments,id',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|string|max:255',
        ]);

        $validated['status'] = 'Pagado';
        $contractedTreatment = ContractedTreatment::find($validated['contracted_treatment_id']);
        $validated['branch_id'] = session('selected_branch_id') ?: ($contractedTreatment->branch_id ?? 1);

        TreatmentOrder::create($validated);

        return redirect()->route('admin.payments.index')->with('success', 'Pago registrado exitosamente.');
    }

    /**
     * Export payments.
     */
    public function export(): RedirectResponse
    {
        return back()->with('success', 'Exportación en construcción');
    }
}

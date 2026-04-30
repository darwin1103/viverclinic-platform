<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingRecord;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountingController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:SuperAdmin|SUPER_ADMIN');
    }

    /**
     * Display a listing of the accounting records and daily closure.
     */
    public function index(): View
    {
        $ingresosDiarios = \App\Models\AccountingRecord::whereIn('type', ['income', 'ingreso'])
            ->whereDate('created_at', today())
            ->sum('amount');

        $egresosDiarios = \App\Models\AccountingRecord::whereIn('type', ['expense', 'egreso'])
            ->whereDate('created_at', today())
            ->sum('amount');

        $ventasTotales = \App\Models\TreatmentOrder::whereIn('status', ['Pagado', 'Paid'])
            ->whereDate('created_at', today())
            ->sum('total');

        $comisionAdmin = max(0, ($ventasTotales / 30) - 2500000);

        return view('admin.accounting.index', compact(
            'ingresosDiarios',
            'egresosDiarios',
            'ventasTotales',
            'comisionAdmin'
        ));
    }

    /**
     * Store a newly created accounting record in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|gt:0',
            'description' => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $branchId = $validated['branch_id'] ?? Branch::first()->id;

        AccountingRecord::create([
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
        ]);

        return redirect()->route('admin.accounting.index')->with('success', 'Registro contable guardado exitosamente.');
    }
}

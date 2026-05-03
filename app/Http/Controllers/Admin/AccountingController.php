<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingRecord;
use App\Models\Branch;
use App\Models\ContractedTreatmentInstallment;
use App\Models\ExpenseCategory;
use App\Models\TreatmentOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AccountingController extends Controller
{
    /**
     * Display a listing of the accounting records with filters.
     */
    public function index(Request $request): View
    {
        $user = Auth::user();
        $isSuperAdmin = $user->hasRole('SUPER_ADMIN') || $user->hasRole('OWNER');

        // Determine branch scope
        $branchId = $request->input('branch_id') ?: session('selected_branch_id');
        if (!$isSuperAdmin && $user->adminsBranches()->exists()) {
            $branchId = $user->adminsBranches()->first()->id;
        }

        // Date range
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());
        $typeFilter = $request->input('type', '');
        $categoryFilter = $request->input('category', '');

        // Build query
        $query = AccountingRecord::with(['user', 'branch'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        if ($typeFilter) {
            $query->where('type', $typeFilter);
        }

        if ($categoryFilter) {
            $query->where('category', $categoryFilter);
        }

        $records = $query->latest()->paginate(20)->withQueryString();

        // KPIs for the period
        $incomeQuery = AccountingRecord::whereIn('type', ['income', 'ingreso'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        $expenseQuery = AccountingRecord::whereIn('type', ['expense', 'egreso'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);

        if ($branchId) {
            $incomeQuery->where('branch_id', $branchId);
            $expenseQuery->where('branch_id', $branchId);
        }

        $totalIncome = $incomeQuery->sum('amount');
        $totalExpense = $expenseQuery->sum('amount');
        $netBalance = $totalIncome - $totalExpense;

        // Pending installments (cuotas pendientes)
        $pendingInstallments = TreatmentOrder::whereIn('status', ['Pendiente', 'Pending', 'Pago por verificar'])
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->sum('total');

        $branches = Branch::all();
        $categories = ExpenseCategory::orderBy('name')->get();
        $selectedBranchID = $branchId ?: '';

        return view('admin.accounting.index', compact(
            'records', 'totalIncome', 'totalExpense', 'netBalance', 'pendingInstallments',
            'branches', 'categories', 'selectedBranchID', 'from', 'to', 'typeFilter',
            'categoryFilter', 'isSuperAdmin'
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
            'category' => 'nullable|string|max:100',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $branchId = $validated['branch_id'] ?? session('selected_branch_id') ?? Branch::first()->id;

        AccountingRecord::create([
            'branch_id' => $branchId,
            'user_id' => Auth::id(),
            'type' => $validated['type'],
            'amount' => $validated['amount'],
            'description' => $validated['description'],
            'category' => $validated['category'] ?? null,
        ]);

        return redirect()->route('admin.accounting.index')->with('success', 'Registro contable guardado exitosamente.');
    }
}

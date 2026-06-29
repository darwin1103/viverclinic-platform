<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingRecord;
use App\Models\AdminProfile;
use App\Models\Branch;
use App\Models\PayrollSettlement;
use App\Models\Sale;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PayrollController extends Controller
{
    /**
     * Display the payroll index with settlements for a given month/year.
     */
    public function index(Request $request)
    {
        $month = $request->input('month', now()->month);
        $year = $request->input('year', now()->year);
        $branchId = session('selected_branch_id');

        // Get existing settlements for this period
        $query = PayrollSettlement::with(['user', 'branch'])
            ->where('period_month', $month)
            ->where('period_year', $year);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $settlements = $query->orderBy('role_type')->orderBy('base_salary', 'desc')->get();

        // Load sales count and total for each employee settlement dynamically
        foreach ($settlements as $settlement) {
            if ($settlement->role_type === 'EMPLOYEE') {
                $sales = Sale::where('staff_user_id', $settlement->user_id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->get();
                $settlement->sales_count = $sales->count();
                $settlement->sales_total = $sales->sum('first_payment_amount');
            } else {
                $settlement->sales_count = 0;
                $settlement->sales_total = 0;
            }
        }

        // KPIs
        $totalToSettle = $settlements->sum('total');
        $pendingCount = $settlements->where('status', 'pending')->count();
        $paidCount = $settlements->where('status', 'paid')->count();

        $branches = Branch::all();

        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        return view('admin.payroll.index', compact(
            'settlements', 'totalToSettle', 'pendingCount', 'paidCount',
            'month', 'year', 'months', 'branches'
        ));
    }

    /**
     * Generate payroll settlements for a given month/year.
     */
    public function generate(Request $request)
    {
        $request->validate([
            'month' => 'required|integer|min:1|max:12',
            'year'  => 'required|integer|min:2020',
        ]);

        $month = $request->month;
        $year = $request->year;
        $branchId = session('selected_branch_id');

        // Check if settlements already exist for this period
        $existingQuery = PayrollSettlement::where('period_month', $month)->where('period_year', $year);
        if ($branchId) {
            $existingQuery->where('branch_id', $branchId);
        }
        if ($existingQuery->exists()) {
            return back()->with('error', 'Ya se generó la liquidación para este periodo. Elimine la existente primero si desea regenerar.');
        }

        DB::transaction(function () use ($month, $year, $branchId) {

            // --- EMPLOYEES ---
            $staffQuery = StaffProfile::with('user');
            if ($branchId) {
                $staffQuery->where('branch_id', $branchId);
            }
            $staffProfiles = $staffQuery->get();

            foreach ($staffProfiles as $profile) {
                $baseSalary = $profile->salary ?? 0;

                PayrollSettlement::create([
                    'branch_id'         => $profile->branch_id,
                    'user_id'           => $profile->user_id,
                    'role_type'         => 'EMPLOYEE',
                    'period_month'      => $month,
                    'period_year'       => $year,
                    'base_salary'       => $baseSalary,
                    'commission_amount' => 0,
                    'total'             => $baseSalary,
                ]);
            }

            // --- ADMINS ---
            $adminQuery = AdminProfile::with('user');
            if ($branchId) {
                $adminQuery->where('branch_id', $branchId);
            }
            $adminProfiles = $adminQuery->get();

            foreach ($adminProfiles as $profile) {
                $baseSalary = $profile->salary ?? 0;

                PayrollSettlement::create([
                    'branch_id'         => $profile->branch_id,
                    'user_id'           => $profile->user_id,
                    'role_type'         => 'ADMIN',
                    'period_month'      => $month,
                    'period_year'       => $year,
                    'base_salary'       => $baseSalary,
                    'commission_amount' => 0,
                    'total'             => $baseSalary,
                ]);
            }

            // --- SALES ---
            $salesUsers = User::role('SALES')->with('salesProfile')->get();
            foreach ($salesUsers as $user) {
                $userBranchId = $user->salesProfile->branch_id ?? session('selected_branch_id');
                if ($branchId && $userBranchId != $branchId) {
                    continue;
                }
                
                if (!$userBranchId) continue;

                // Base salary for sales is 0 by default, commissions are manually added later
                PayrollSettlement::create([
                    'branch_id'         => $userBranchId,
                    'user_id'           => $user->id,
                    'role_type'         => 'SALES',
                    'period_month'      => $month,
                    'period_year'       => $year,
                    'base_salary'       => 0,
                    'commission_amount' => 0,
                    'total'             => 0,
                ]);
            }
        });

        return back()->with('success', 'Liquidación generada exitosamente para el periodo seleccionado.');
    }

    /**
     * Mark a settlement as paid and create an accounting expense record.
     */
    public function markAsPaid(PayrollSettlement $settlement)
    {
        if ($settlement->status === 'paid') {
            return back()->with('info', 'Esta liquidación ya fue pagada.');
        }

        DB::transaction(function () use ($settlement) {
            $settlement->update([
                'status'  => 'paid',
                'paid_at' => now(),
            ]);

            // Create accounting record as expense
            AccountingRecord::create([
                'branch_id'      => $settlement->branch_id,
                'user_id'        => $settlement->user_id,
                'type'           => 'expense',
                'amount'         => $settlement->total,
                'description'    => "Liquidación {$settlement->role_type} - {$settlement->user->name} ({$settlement->period_month}/{$settlement->period_year})",
                'category'       => 'Nómina',
                'reference_id'   => $settlement->id,
                'reference_type' => PayrollSettlement::class,
            ]);
        });

        return back()->with('success', 'Liquidación marcada como pagada. Se registró el egreso en contabilidad.');
    }

    /**
     * Display detailed view of a settlement with all commission entries.
     */
    public function show(PayrollSettlement $settlement)
    {
        $settlement->load(['user', 'branch', 'manualBonuses']);

        $month = $settlement->period_month;
        $year = $settlement->period_year;
        $userId = $settlement->user_id;

        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Sales entries
        $sales = Sale::with(['patient'])
            ->where('staff_user_id', $userId)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
        $salesCount = $sales->count();
        $salesTotal = $sales->sum('first_payment_amount');

        return view('admin.payroll.show', compact(
            'settlement', 'months', 'month', 'year', 'sales', 'salesCount', 'salesTotal'
        ));
    }
    
    /**
     * Update the manual commission amount for a settlement.
     */
    public function updateCommission(Request $request, PayrollSettlement $settlement)
    {
        $request->validate([
            'commission_amount' => 'required|numeric|min:0',
        ]);
        
        $settlement->update([
            'commission_amount' => $request->commission_amount
        ]);
        
        $settlement->recalculateTotal();
        
        return back()->with('success', 'Comisión actualizada correctamente.');
    }

    /**
     * Add a manual bonus entry to a settlement.
     */
    public function addManualBonus(Request $request, PayrollSettlement $settlement)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        $settlement->manualBonuses()->create([
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        $settlement->recalculateTotal();

        return back()->with('success', 'Bono manual agregado correctamente.');
    }

    /**
     * Update a manual bonus entry.
     */
    public function updateManualBonusEntry(Request $request, PayrollSettlement $settlement, \App\Models\ManualBonus $bonus)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
        ]);

        $bonus->update([
            'amount' => $request->amount,
            'note' => $request->note,
        ]);

        $settlement->recalculateTotal();

        return back()->with('success', 'Bono manual actualizado correctamente.');
    }

    /**
     * Delete a manual bonus entry.
     */
    public function deleteManualBonusEntry(PayrollSettlement $settlement, \App\Models\ManualBonus $bonus)
    {
        $bonus->delete();
        $settlement->recalculateTotal();

        return back()->with('success', 'Bono manual eliminado correctamente.');
    }
}

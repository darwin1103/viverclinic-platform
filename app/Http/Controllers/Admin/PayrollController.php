<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountingRecord;
use App\Models\AdminProfile;
use App\Models\Branch;
use App\Models\PayrollSettlement;
use App\Models\Referral;
use App\Models\StaffProfile;
use App\Models\TreatmentOrder;
use App\Models\User;
use App\Models\PackageUpgrade;
use App\Models\RepurchaseCommission;
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
                // Calculate referral commissions for this employee in this month
                $referralCommissions = Referral::where('staff_id', $profile->user_id)
                    ->where('status', 'rewarded')
                    ->whereMonth('rewarded_at', $month)
                    ->whereYear('rewarded_at', $year)
                    ->sum('staff_commission');

                // Calculate package upgrade commissions for this employee in this month
                $upgradeCommissions = PackageUpgrade::where('staff_user_id', $profile->user_id)
                    ->where('payment_status', 'APPROVED')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('commission_amount');

                // Calculate repurchase commissions for this employee in this month
                $repurchaseCommissions = RepurchaseCommission::where('staff_user_id', $profile->user_id)
                    ->where('status', 'approved')
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('commission_amount');

                $baseSalary = $profile->salary ?? 0;
                $total = $baseSalary + $referralCommissions + $upgradeCommissions + $repurchaseCommissions;

                PayrollSettlement::create([
                    'branch_id'             => $profile->branch_id,
                    'user_id'               => $profile->user_id,
                    'role_type'             => 'EMPLOYEE',
                    'period_month'          => $month,
                    'period_year'           => $year,
                    'base_salary'           => $baseSalary,
                    'referral_commissions'  => $referralCommissions,
                    'upgrade_commissions'   => $upgradeCommissions,
                    'repurchase_commissions' => $repurchaseCommissions,
                    'sales_commissions'     => 0,
                    'total'                 => $total,
                ]);
            }

            // --- ADMINS ---
            $adminQuery = AdminProfile::with('user');
            if ($branchId) {
                $adminQuery->where('branch_id', $branchId);
            }
            $adminProfiles = $adminQuery->get();

            foreach ($adminProfiles as $profile) {
                // Calculate sales commission: (ventas_mes / divisor) - base, minimum 0
                $monthlySales = TreatmentOrder::whereIn('status', ['Pagado', 'Pago completado', 'Paid', 'Completado'])
                    ->where('branch_id', $profile->branch_id)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('total');

                $divisor = $profile->commission_divisor ?: 30;
                $base = $profile->commission_base ?? 2500000;
                $salesCommission = max(0, ($monthlySales / $divisor) - $base);

                $baseSalary = $profile->salary ?? 0;
                $total = $baseSalary + $salesCommission;

                PayrollSettlement::create([
                    'branch_id'             => $profile->branch_id,
                    'user_id'               => $profile->user_id,
                    'role_type'             => 'ADMIN',
                    'period_month'          => $month,
                    'period_year'           => $year,
                    'base_salary'           => $baseSalary,
                    'referral_commissions'  => 0,
                    'sales_commissions'     => $salesCommission,
                    'total'                 => $total,
                ]);
            }

            // --- SALES ---
            $salesUsers = User::role('SALES')->with('salesProfile')->get();
            foreach ($salesUsers as $user) {
                // Determine branch from salesProfile if exists, else skip or use session
                $userBranchId = $user->salesProfile->branch_id ?? session('selected_branch_id');
                if ($branchId && $userBranchId != $branchId) {
                    continue; // Skip if filtering by branch and it doesn't match
                }
                
                if (!$userBranchId) continue; // Cannot determine branch

                $divisor = $user->salesProfile->commission_divisor ?? 26;
                $divisor = max(1, $divisor); // Ensure divisor is at least 1

                // Calculate commission: ventas totales paquetes / divisor
                $monthlyPackagesSales = TreatmentOrder::whereIn('status', ['Pagado', 'Pago completado', 'Paid', 'Completado', 'Aprobado', 'APPROVED'])
                    ->where('branch_id', $userBranchId)
                    ->whereMonth('created_at', $month)
                    ->whereYear('created_at', $year)
                    ->sum('total');

                $salesCommission = max(0, $monthlyPackagesSales / $divisor);
                
                // Net commissions (0 base salary)
                $total = $salesCommission;

                if ($total > 0) {
                    PayrollSettlement::create([
                        'branch_id'             => $userBranchId,
                        'user_id'               => $user->id,
                        'role_type'             => 'SALES',
                        'period_month'          => $month,
                        'period_year'           => $year,
                        'base_salary'           => 0,
                        'referral_commissions'  => 0,
                        'sales_commissions'     => $salesCommission,
                        'total'                 => $total,
                    ]);
                }
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
        $settlement->load(['user', 'branch']);

        $month = $settlement->period_month;
        $year = $settlement->period_year;
        $userId = $settlement->user_id;

        $months = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];

        // Referral commission entries
        $referralEntries = Referral::with(['referred'])
            ->where('staff_id', $userId)
            ->where('status', 'rewarded')
            ->whereMonth('rewarded_at', $month)
            ->whereYear('rewarded_at', $year)
            ->get();

        // Upgrade commission entries
        $upgradeEntries = PackageUpgrade::with(['contractedTreatment.user', 'contractedTreatment.treatment'])
            ->where('staff_user_id', $userId)
            ->where('payment_status', 'APPROVED')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        // Repurchase commission entries
        $repurchaseEntries = RepurchaseCommission::with(['contractedTreatment.user', 'contractedTreatment.treatment'])
            ->where('staff_user_id', $userId)
            ->where('status', 'approved')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        return view('admin.payroll.show', compact(
            'settlement', 'months', 'month', 'year',
            'referralEntries', 'upgradeEntries', 'repurchaseEntries'
        ));
    }

    /**
     * Update manual bonus for a settlement.
     */
    public function updateManualBonus(Request $request, PayrollSettlement $settlement)
    {
        $request->validate([
            'manual_bonus' => 'required|numeric|min:0',
            'manual_bonus_note' => 'nullable|string|max:255',
        ]);

        $oldBonus = $settlement->manual_bonus ?? 0;
        $newBonus = $request->manual_bonus;

        // Recalculate total: remove old bonus, add new bonus
        $newTotal = $settlement->total - $oldBonus + $newBonus;

        $settlement->update([
            'manual_bonus' => $newBonus,
            'manual_bonus_note' => $request->manual_bonus_note,
            'total' => $newTotal,
        ]);

        return back()->with('success', 'Bono manual actualizado correctamente para ' . ($settlement->user->name ?? ''));
    }
}

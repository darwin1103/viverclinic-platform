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

                $baseSalary = $profile->salary ?? 0;
                $total = $baseSalary + $referralCommissions;

                PayrollSettlement::create([
                    'branch_id'             => $profile->branch_id,
                    'user_id'               => $profile->user_id,
                    'role_type'             => 'EMPLOYEE',
                    'period_month'          => $month,
                    'period_year'           => $year,
                    'base_salary'           => $baseSalary,
                    'referral_commissions'  => $referralCommissions,
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
}

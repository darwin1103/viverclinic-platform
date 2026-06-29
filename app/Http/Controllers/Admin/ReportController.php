<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TreatmentOrder;
use App\Models\ContractedTreatment;
use App\Models\Appointment;
use App\Models\User;
use App\Models\Referral;
use App\Models\Sale;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id') ?: session('selected_branch_id');
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        // --- 1. Monthly Performance (existing) ---
        $monthField = config('database.default') === 'sqlite' 
            ? "strftime('%m', created_at)" 
            : "MONTH(created_at)";

        $monthlyQuery = TreatmentOrder::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed', 'Pago completado'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw("$monthField as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month');

        if ($branchId) {
            $monthlyQuery->where('branch_id', $branchId);
        }

        $monthlyPerformance = $monthlyQuery->get();

        // --- 2. Top Treatments (existing) ---
        $topTreatmentsQuery = ContractedTreatment::with('treatment')
            ->selectRaw('treatment_id, COUNT(*) as count')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->groupBy('treatment_id')
            ->orderByDesc('count')
            ->take(5);

        if ($branchId) {
            $topTreatmentsQuery->where('branch_id', $branchId);
        }

        $topTreatments = $topTreatmentsQuery->get();

        // --- 3. Appointment Summary (filtered) ---
        $appointmentQuery = Appointment::whereDate('schedule', '>=', $from)
            ->whereDate('schedule', '<=', $to);

        if ($branchId) {
            $appointmentQuery->where('branch_id', $branchId);
        }

        $totalAppointments = (clone $appointmentQuery)->count();
        $attendedAppointments = (clone $appointmentQuery)->whereIn('status', ['Atendida', 'Completada'])->count();
        $cancelledAppointments = (clone $appointmentQuery)->where('status', 'Cancelada')->count();
        $noShowAppointments = (clone $appointmentQuery)->where('status', 'No asistió')->count();
        $attendanceRate = $totalAppointments > 0 ? round(($attendedAppointments / $totalAppointments) * 100, 1) : 0;

        // --- 4. Staff Performance ---
        $staffPerformance = User::role('EMPLOYEE')
            ->withCount(['assignedAppointments as completed_count' => function ($q) use ($branchId, $from, $to) {
                $q->whereIn('status', ['Atendida', 'Completada'])
                  ->whereDate('schedule', '>=', $from)
                  ->whereDate('schedule', '<=', $to);
                if ($branchId) {
                    $q->whereHas('contractedTreatment', function ($sub) use ($branchId) {
                        $sub->where('branch_id', $branchId);
                    });
                }
            }])
            ->withAvg(['assignedAppointments as avg_rating' => function ($q) use ($branchId, $from, $to) {
                $q->whereNotNull('review_score')
                  ->whereDate('schedule', '>=', $from)
                  ->whereDate('schedule', '<=', $to);
                if ($branchId) {
                    $q->whereHas('contractedTreatment', function ($sub) use ($branchId) {
                        $sub->where('branch_id', $branchId);
                    });
                }
            }], 'review_score')
            ->get()
            ->where('completed_count', '>', 0)
            ->sortByDesc('completed_count')
            ->take(10);

        // --- 5. NEW KPIs (Requested by user) ---
        // 1. Nuevos pacientes
        $newPatientsQuery = User::role('PATIENT')
            ->where('is_legacy', false)
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        // Note: Users don't strictly belong to a branch globally except through pivots, but we'll show global or pivot based.
        // Assuming global new patients for simplicity if no branch pivot is readily available in this context without joins.
        $newPatients = $newPatientsQuery->count();

        // 2. Pacientes finalizados (Tratamientos completados)
        $finishedPatientsQuery = ContractedTreatment::where('status', 'Completed')
            ->whereDate('updated_at', '>=', $from)
            ->whereDate('updated_at', '<=', $to);
        if ($branchId) $finishedPatientsQuery->where('branch_id', $branchId);
        $finishedPatients = $finishedPatientsQuery->count();

        // 3. Ingreso de tratamientos
        $treatmentIncomeQuery = TreatmentOrder::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed', 'Pago completado', 'Aprobado', 'APPROVED'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        if ($branchId) $treatmentIncomeQuery->where('branch_id', $branchId);
        $treatmentIncome = $treatmentIncomeQuery->sum('total');

        // 4. Ingreso de productos
        $productIncomeQuery = \App\Models\Order::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        if ($branchId) $productIncomeQuery->where('branch_id', $branchId);
        $productIncome = $productIncomeQuery->sum('total');

        // 5. Gastos totales
        $expensesQuery = \App\Models\AccountingRecord::whereIn('type', ['expense', 'egreso'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        if ($branchId) $expensesQuery->where('branch_id', $branchId);
        $totalExpenses = $expensesQuery->sum('amount');

        // 6. Pacientes atendidos (attendedAppointments variable already has this)
        $attendedPatients = $attendedAppointments;

        // Legacy retention/conversion (updated to use dates)
        $recurringPatients = ContractedTreatment::selectRaw('user_id, COUNT(*) as total')
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $convertedReferrals = Referral::where('status', 'rewarded')
            ->whereDate('rewarded_at', '>=', $from)
            ->whereDate('rewarded_at', '<=', $to)
            ->count();

        // Sales KPIs (Referrals, Upgrades, Repurchases)
        $salesQuery = Sale::whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to);
        if ($branchId) {
            $salesQuery->where('branch_id', $branchId);
        }
        $salesCount = (clone $salesQuery)->count();
        $salesIncome = (clone $salesQuery)->sum('first_payment_amount');

        $salesByEmployee = (clone $salesQuery)
            ->with('staff')
            ->selectRaw('staff_user_id, count(*) as count, sum(first_payment_amount) as total')
            ->groupBy('staff_user_id')
            ->orderByDesc('total')
            ->get();

        // --- 6. Revenue by Payment Method ---
        $revenueByMethodQuery = TreatmentOrder::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed', 'Pago completado', 'Aprobado', 'APPROVED'])
            ->whereDate('created_at', '>=', $from)
            ->whereDate('created_at', '<=', $to)
            ->selectRaw("COALESCE(payment_method, 'Sin definir') as method, SUM(total) as total, COUNT(*) as count")
            ->groupBy('method')
            ->orderByDesc('total');

        if ($branchId) {
            $revenueByMethodQuery->where('branch_id', $branchId);
        }

        $revenueByMethod = $revenueByMethodQuery->get();
        $maxMethodTotal = $revenueByMethod->max('total') ?: 1;

        // --- 7. Shots Control ---
        $shotsQuery = Appointment::with(['staff', 'contractedTreatment.treatment', 'contractedTreatment.user'])
            ->whereNotNull('uses_of_hair_removal_shots')
            ->whereDate('schedule', '>=', $from)
            ->whereDate('schedule', '<=', $to);

        // Branch filter is applied by global scope, but we can enforce if needed or let ScopesByBranch handle it.
        // Actually, ScopesByBranch handles appointments using contracted_treatments.branch_id
        if ($branchId) {
            $shotsQuery->whereHas('contractedTreatment', function ($q) use ($branchId) {
                $q->where('branch_id', $branchId);
            });
        }

        $shotsRecords = $shotsQuery->orderBy('schedule', 'desc')->get();

        $branches = \App\Models\Branch::all();
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.reports.index', compact(
            'from', 'to',
            'monthlyPerformance', 'topTreatments', 'branches', 'selectedBranchID',
            'totalAppointments', 'attendedAppointments', 'cancelledAppointments',
            'noShowAppointments', 'attendanceRate',
            'staffPerformance',
            'newPatients', 'finishedPatients', 'treatmentIncome', 'productIncome', 'totalExpenses', 'attendedPatients',
            'recurringPatients', 'convertedReferrals',
            'salesCount', 'salesIncome', 'salesByEmployee',
            'revenueByMethod', 'maxMethodTotal',
            'shotsRecords'
        ));
    }

    public function staffDetail(Request $request, User $user)
    {
        // Parse dates to match the report timeframe
        $from = $request->get('from') ? Carbon::parse($request->get('from'))->startOfDay() : now()->startOfMonth();
        $to = $request->get('to') ? Carbon::parse($request->get('to'))->endOfDay() : now()->endOfDay();
        
        $branchId = $request->get('branch_id', session('selected_branch_id', ''));

        $appointmentsQuery = \App\Models\Appointment::with(['contractedTreatment.user', 'contractedTreatment.treatment'])
            ->where('staff_user_id', $user->id)
            ->whereIn('status', ['Atendida', 'Completada'])
            ->whereDate('schedule', '>=', $from)
            ->whereDate('schedule', '<=', $to);

        if ($branchId) {
            $appointmentsQuery->whereHas('contractedTreatment', function ($sub) use ($branchId) {
                $sub->where('branch_id', $branchId);
            });
        }

        // Ordered by latest first, paginated
        $appointments = $appointmentsQuery->orderBy('schedule', 'desc')->paginate(15);

        return view('admin.reports.staff_detail', compact('user', 'appointments', 'from', 'to'));
    }
}

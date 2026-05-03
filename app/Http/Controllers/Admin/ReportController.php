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

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id') ?: session('selected_branch_id');

        // --- 1. Monthly Performance (existing) ---
        $monthlyQuery = TreatmentOrder::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed', 'Pago completado'])
            ->whereYear('created_at', now()->year)
            ->selectRaw("strftime('%m', created_at) as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month');

        if ($branchId) {
            $monthlyQuery->where('branch_id', $branchId);
        }

        $monthlyPerformance = $monthlyQuery->get();

        // --- 2. Top Treatments (existing) ---
        $topTreatmentsQuery = ContractedTreatment::with('treatment')
            ->selectRaw('treatment_id, COUNT(*) as count')
            ->groupBy('treatment_id')
            ->orderByDesc('count')
            ->take(5);

        if ($branchId) {
            $topTreatmentsQuery->where('branch_id', $branchId);
        }

        $topTreatments = $topTreatmentsQuery->get();

        // --- 3. Appointment Summary (current month) ---
        $appointmentQuery = Appointment::whereMonth('schedule', now()->month)
            ->whereYear('schedule', now()->year);

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
            ->withCount(['assignedAppointments as completed_count' => function ($q) use ($branchId) {
                $q->whereIn('status', ['Atendida', 'Completada'])
                  ->whereMonth('schedule', now()->month)
                  ->whereYear('schedule', now()->year);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            }])
            ->withAvg(['assignedAppointments as avg_rating' => function ($q) use ($branchId) {
                $q->whereNotNull('review_score')
                  ->whereMonth('schedule', now()->month)
                  ->whereYear('schedule', now()->year);
                if ($branchId) {
                    $q->where('branch_id', $branchId);
                }
            }], 'review_score')
            ->get()
            ->where('completed_count', '>', 0)
            ->sortByDesc('completed_count')
            ->take(10);

        // --- 5. Patient Retention ---
        $newPatients = User::role('PATIENT')
            ->where('created_at', '>=', now()->subDays(30))
            ->count();

        $recurringPatients = ContractedTreatment::selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->count();

        $convertedReferrals = Referral::where('status', 'rewarded')->count();

        // --- 6. Revenue by Payment Method ---
        $revenueByMethodQuery = TreatmentOrder::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed', 'Pago completado'])
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->selectRaw("COALESCE(payment_method, 'Sin definir') as method, SUM(total) as total, COUNT(*) as count")
            ->groupBy('method')
            ->orderByDesc('total');

        if ($branchId) {
            $revenueByMethodQuery->where('branch_id', $branchId);
        }

        $revenueByMethod = $revenueByMethodQuery->get();
        $maxMethodTotal = $revenueByMethod->max('total') ?: 1;

        $branches = \App\Models\Branch::all();
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.reports.index', compact(
            'monthlyPerformance', 'topTreatments', 'branches', 'selectedBranchID',
            'totalAppointments', 'attendedAppointments', 'cancelledAppointments',
            'noShowAppointments', 'attendanceRate',
            'staffPerformance',
            'newPatients', 'recurringPatients', 'convertedReferrals',
            'revenueByMethod', 'maxMethodTotal'
        ));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\TreatmentOrder;
use App\Models\ContractedTreatment;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(Request $request): View
    {
        $branchId = $request->input('branch_id') ?: session('selected_branch_id');

        $monthlyQuery = TreatmentOrder::whereIn('status', ['Pagado', 'Completado', 'Paid', 'Completed'])
            ->whereYear('created_at', now()->year)
            ->selectRaw("strftime('%m', created_at) as month, SUM(total) as total")
            ->groupBy('month')
            ->orderBy('month');

        if ($branchId) {
            $monthlyQuery->where('branch_id', $branchId);
        }

        $monthlyPerformance = $monthlyQuery->get();

        $topTreatmentsQuery = ContractedTreatment::with('treatment')
            ->selectRaw('treatment_id, COUNT(*) as count')
            ->groupBy('treatment_id')
            ->orderByDesc('count')
            ->take(5);

        if ($branchId) {
            $topTreatmentsQuery->where('branch_id', $branchId);
        }

        $topTreatments = $topTreatmentsQuery->get();

        $branches = \App\Models\Branch::all();
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.reports.index', compact('monthlyPerformance', 'topTreatments', 'branches', 'selectedBranchID'));
    }
}

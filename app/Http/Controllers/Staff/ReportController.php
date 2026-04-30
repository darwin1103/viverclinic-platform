<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\Appointment;
use App\Models\User;

class ReportController extends Controller
{
    /**
     * Display a listing of the reports.
     */
    public function index(): View
    {
        $branchId = auth()->user()->staffProfile->branch_id ?? null;

        $currentMonthAppointments = Appointment::whereHas('contractedTreatment', function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->whereMonth('schedule', now()->month)
            ->whereYear('schedule', now()->year)
            ->count();
            
        $newPatientsLast30Days = User::role('PATIENT')
            ->whereHas('patientsBranches', function ($query) use ($branchId) {
                $query->where('branches.id', $branchId);
            })
            ->where('users.created_at', '>=', now()->subDays(30))
            ->count();

        return view('staff.reports.index', compact('currentMonthAppointments', 'newPatientsLast30Days'));
    }
}

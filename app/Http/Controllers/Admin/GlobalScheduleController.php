<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GlobalSchedule;
use App\Models\Holiday;
use App\Models\Setting;
use App\Models\User;
use App\Models\Branch;

class GlobalScheduleController extends Controller
{
    public function index()
    {
        // For simplicity we assume a single active branch for the logged in user, or just fetch the first
        // If the platform uses scopes by branch, it will auto-filter.
        $branch = Branch::first(); // Or session branch if implemented
        $branchId = $branch ? $branch->id : 1;

        $schedules = GlobalSchedule::where('branch_id', $branchId)->get()->groupBy('day_of_week');
        $holidays = Holiday::orderBy('date', 'desc')->get();
        $employees = User::role('EMPLOYEE')->get();

        $daysOfWeek = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];

        // Slots globales desde settings
        $regularSlots = Setting::get('regular_slots', '0');
        $salesSlots = Setting::get('sales_slots', '0');

        return view('admin.global-schedule.index', compact('schedules', 'holidays', 'employees', 'daysOfWeek', 'branchId', 'regularSlots', 'salesSlots'));
    }

    public function storeSchedule(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
            'schedules' => 'array',
        ]);

        $branchId = $request->branch_id;

        // Limpiar los horarios actuales para la sucursal
        GlobalSchedule::where('branch_id', $branchId)->delete();

        if ($request->has('schedules') && is_array($request->schedules)) {
            foreach ($request->schedules as $day => $blocks) {
                foreach ($blocks as $block) {
                    if (!empty($block['start_time']) && !empty($block['end_time'])) {
                        GlobalSchedule::create([
                            'branch_id' => $branchId,
                            'day_of_week' => $day,
                            'start_time' => $block['start_time'],
                            'end_time' => $block['end_time'],
                        ]);
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Horarios actualizados correctamente.');
    }

    public function storeSlots(Request $request)
    {
        $request->validate([
            'regular_slots' => 'required|integer|min:0',
            'sales_slots' => 'required|integer|min:0',
        ]);

        Setting::updateOrCreate(['key' => 'regular_slots'], ['value' => $request->regular_slots]);
        Setting::updateOrCreate(['key' => 'sales_slots'], ['value' => $request->sales_slots]);

        return redirect()->back()->with('success', 'Cupos actualizados correctamente.');
    }

    public function toggleEmployeeStatus(Request $request, User $user)
    {
        $request->validate([
            'is_enabled' => 'required|boolean'
        ]);

        $user->is_enabled_for_appointments = $request->is_enabled;
        $user->save();

        return response()->json(['success' => true, 'message' => 'Estado de empleado actualizado.']);
    }
}

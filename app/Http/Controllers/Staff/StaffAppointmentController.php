<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAppointmentController extends Controller
{

    /**
     * Display the appointment management page
     */
    public function index(Request $request)
    {
        // Get the currently authenticated staff user's ID
        $staffUserId = Auth::id();

        // Start query for appointments assigned to the current staff member
        $query = Appointment::where('staff_user_id', $staffUserId)
                            ->with(['contractedTreatment.user', 'contractedTreatment.treatment']);

        // Apply search filter for patient name
        if ($request->filled('search')) {
            $query->whereHas('contractedTreatment.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply filter for treatment
        if ($request->filled('treatment_id')) {
            $query->whereHas('contractedTreatment', function ($q) use ($request) {
                $q->where('treatment_id', $request->treatment_id);
            });
        }

        // Apply date filters
        if ($request->filled('min_date')) {
            $query->whereDate('schedule', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $query->whereDate('schedule', '<=', $request->max_date);
        }

        // Order by most recent schedule and paginate
        $appointments = $query->orderBy('schedule', 'desc')->paginate(15);

        // Get all treatments for the filter dropdown
        $treatments = Treatment::orderBy('name')->get();

        // Pass data to the view
        $data = [
            'appointments' => $appointments,
            'treatments' => $treatments,
        ];

        return view('staff.appointment.index', $data);
    }

    public function setAppointmnetShots(Appointment $appointment, Request $request)
    {

        $user = Auth::user();

        if(
            intval($appointment->uses_of_hair_removal_shots) > 0 ||
            $appointment->staff_user_id != $user->id
        ){
            abort(403);
        }

        $validated = $request->validate([
            'shots' => 'required|integer|min:1',
        ]);

        $appointment->update([
            'uses_of_hair_removal_shots' => $validated['shots'],
        ]);

        return redirect()->back()->with('success', 'informacion guardada exitosamente');

    }

}

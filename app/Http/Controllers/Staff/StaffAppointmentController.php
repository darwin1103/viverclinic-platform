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
                            ->with([
                                'contractedTreatment.user' => function ($q) {
                                    $q->withoutGlobalScope('scopesByBranch');
                                },
                                'contractedTreatment.treatment'
                            ]);

        $tab = $request->input('tab', 'today');

        if ($tab === 'today') {
            // Citas del día actual (y futuras, para incluir datos de prueba)
            $query->whereDate('schedule', '>=', now()->toDateString());
            
            // Ordenar primero por 'Atendida', luego 'Asignada', luego el resto, y por hora
            $query->orderByRaw("CASE WHEN status = 'Atendida' THEN 1 WHEN status = 'Asignada' THEN 2 ELSE 3 END")
                  ->orderBy('schedule', 'asc');
        } else {
            // Archivo: citas de días anteriores
            $query->whereDate('schedule', '<', now()->toDateString());
            $query->orderBy('schedule', 'desc');
        }

        // Apply search filter for patient name
        if ($request->filled('search')) {
            $query->whereHas('contractedTreatment.user', function ($q) use ($request) {
                $q->withoutGlobalScope('scopesByBranch')->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply filter for treatment
        if ($request->filled('treatment_id')) {
            $query->whereHas('contractedTreatment', function ($q) use ($request) {
                $q->where('treatment_id', $request->treatment_id);
            });
        }

        // Paginate
        $appointments = $query->paginate(15)->appends(['tab' => $tab, 'search' => $request->search, 'treatment_id' => $request->treatment_id]);

        // Get all treatments for the filter dropdown
        $treatments = Treatment::orderBy('name')->get();

        // Pass data to the view
        $data = [
            'appointments' => $appointments,
            'treatments' => $treatments,
            'tab' => $tab,
        ];

        return view('staff.appointment.index', $data);
    }

    public function markAppointmnetAsCompleted(Appointment $appointment, Request $request)
    {
        $user = Auth::user();

        if(
            $appointment->status != 'Atendida' ||
            $appointment->staff_user_id != $user->id
        ){
            abort(403);
        }

        $rules = [];
        $needsShots = $appointment->contractedTreatment?->treatment?->needs_report_shots;
        
        if ($needsShots) {
            $rules['shots'] = 'required|integer|min:1';
        }

        $validated = $request->validate($rules);

        $updateData = ['status' => 'Completada'];

        if ($needsShots && isset($validated['shots'])) {
            $updateData['uses_of_hair_removal_shots'] = $validated['shots'];
        }

        $appointment->update($updateData);

        return redirect()->back()->with('success', 'Cita completada exitosamente');
    }

}

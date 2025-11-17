<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\StaffProfile;
use App\Models\Treatment;
use App\Models\User;
use App\Traits\CalculatesAvailableSlots;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAppointmentController extends Controller
{
    use CalculatesAvailableSlots;

    /**
     * Display the appointment management page
     */
    public function index()
    {
        // Get current user's branch (assume admin is associated with a branch)
        $currentBranch = auth()->user()->staffProfile->branch ?? Branch::first();

        return view('admin.appointments.index', [
            'currentBranch' => $currentBranch,
        ]);
    }

    /**
     * Fetch appointments for a given date range
     */
    public function fetch(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'required|exists:branches,id',
            'staff_id' => 'nullable|exists:users,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'status' => 'nullable|string',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();
        $branchId = $validated['branch_id'];

        $query = Appointment::with([
            'contractedTreatment.user',
            'contractedTreatment.treatment',
            'contractedTreatment.branch',
            'staff'
        ])
        ->whereHas('contractedTreatment', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->whereBetween('schedule', [$startDate, $endDate]);

        // Filter by staff
        if (!empty($validated['staff_id'])) {
            $query->where('staff_user_id', $validated['staff_id']);
        }

        // Filter by treatment
        if (!empty($validated['treatment_id'])) {
            $query->whereHas('contractedTreatment', function ($q) use ($validated) {
                $q->where('treatment_id', $validated['treatment_id']);
            });
        }

        // Filter by status
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Search by patient name or email
        if (!empty($validated['search'])) {
            $search = $validated['search'];
            $query->whereHas('contractedTreatment.user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $appointments = $query->orderBy('schedule', 'asc')->get();

        // Format appointments for frontend
        $formatted = $appointments->map(function ($appointment) {
            $schedule = Carbon::parse($appointment->schedule);
            $duration = 20; // Default duration in minutes, adjust as needed

            return [
                'id' => $appointment->id,
                'date' => $schedule->format('Y-m-d'),
                'start' => $schedule->format('h:i a'),
                'duration' => $duration,
                'patient' => $appointment->contractedTreatment->user->name,
                'patient_email' => $appointment->contractedTreatment->user->email,
                'professional' => $appointment->staff ? $appointment->staff->name : 'Sin asignar',
                'treatment' => $appointment->contractedTreatment->treatment->name,
                'status' => $appointment->status,
                'attended' => $appointment->attended,
                'session_number' => $appointment->session_number,
                'review_score' => $appointment->review_score,
            ];
        });

        return response()->json(['appointments' => $formatted]);
    }

    /**
     * Get staff list for filters
     */
    public function getStaffList()
    {

        $branchId = request()->get('branch_id');

        $staff = User::whereHas('staffProfile', function ($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
        })->select('id', 'name')->get();

        return response()->json(['staff' => $staff]);

    }

    /**
     * Get treatments list for filters
     */
    public function getTreatmentsList()
    {
        $treatments = Treatment::select('id', 'name')
            ->where('active', true)
            ->orderBy('name')
            ->get();

        return response()->json(['treatments' => $treatments]);
    }

    /**
     * Mark appointment as attended and assign staff
     */
    public function markAsAttended(Appointment $appointment, Request $request)
    {
        $validated = $request->validate([
            'attended' => 'required|boolean',
        ]);

        DB::beginTransaction();
        try {
            // Mark as attended
            $appointment->attended = $validated['attended'];
            $appointment->status = 'Atendida';

            // Assign staff member if not already assigned
            if (!$appointment->staff_user_id && $validated['attended']) {
                $staffId = $this->assignStaffSequentially($appointment);
                $appointment->staff_user_id = $staffId;
            }

            $appointment->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Cita marcada como atendida',
                'appointment' => $appointment->load(['staff', 'contractedTreatment.user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Assign staff member sequentially for fair distribution
     */
    private function assignStaffSequentially(Appointment $appointment)
    {
        $branchId = $appointment->contractedTreatment->branch_id;
        $appointmentTime = Carbon::parse($appointment->schedule);

        // Get staff members who work at this branch
        $staffMembers = User::whereHas('staffProfile', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })
        ->whereHas('staffProfile.workSchedules', function ($q) use ($appointmentTime) {
            $dayOfWeek = $appointmentTime->locale('es')->dayName; // e.g., 'Lunes', 'Martes'
            $time = $appointmentTime->format('H:i:s');

            $q->where('day_of_week', $dayOfWeek)
              ->where('start_time', '<=', $time)
              ->where('end_time', '>=', $time);
        })
        ->withCount(['appointments as appointments_count' => function ($q) {
            $q->whereNotNull('attended')
              ->where('attended', true);
        }])
        ->orderBy('appointments_count', 'asc')
        ->first();

        if ($staffMembers) {
            return $staffMembers->id;
        }

        // Fallback: return any staff member from the branch
        $fallbackStaff = User::whereHas('staffProfile', function ($q) use ($branchId) {
            $q->where('branch_id', $branchId);
        })->first();

        return $fallbackStaff ? $fallbackStaff->id : null;
    }

    /**
     * Confirm appointment
     */
    public function confirm(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'Confirmada'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita confirmada exitosamente'
        ]);
    }

    /**
     * Reschedule appointment
     */
    public function reschedule(Appointment $appointment, Request $request)
    {
        $validated = $request->validate([
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        $date = $validated['appointment_date'];
        $time = $validated['appointment_time'];
        $schedule = Carbon::parse($date . ' ' . $time);

        // Determine status based on time
        $now = Carbon::now();
        $next24Hours = $now->copy()->addHours(24);
        $status = $schedule->between($now, $next24Hours) ? 'Confirmada' : 'Por confirmar';

        $appointment->update([
            'schedule' => $schedule->toDateTimeString(),
            'status' => $status,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita reagendada exitosamente'
        ]);
    }

    /**
     * Cancel appointment
     */
    public function cancel(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'Cancelada'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cita cancelada exitosamente'
        ]);
    }

    /**
     * Get available slots for rescheduling
     */
    public function availableSlots(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date_format:Y-m-d|after_or_equal:today',
            'branch_id' => 'required|integer|exists:branches,id',
        ]);

        try {
            $date = Carbon::parse($validated['date']);
            $branchId = (int)$validated['branch_id'];

            $slots = $this->calculateAvailableSlots($date, $branchId);

            return response()->json(['slots' => $slots]);
        } catch (\Exception $e) {
            \Log::error('Error calculating available slots: ' . $e->getMessage());
            return response()->json(['error' => 'No se pudieron obtener los horarios disponibles.'], 500);
        }
    }
}

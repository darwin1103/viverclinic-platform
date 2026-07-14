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
use App\Http\Requests\Admin\UpdateAppointmentRequest;
use App\Services\NotificationService;

class AdminAppointmentController extends Controller
{
    use CalculatesAvailableSlots;

    /**
     * Show the form for creating a new appointment manually
     */
    public function create(): \Illuminate\View\View
    {
        $patients = User::role('PATIENT')->select(['id', 'name', 'email'])->get();
        $contractedTreatments = \App\Models\ContractedTreatment::with(['treatment', 'user', 'branch'])
            ->whereIn('status', ['Activo', 'Pending', 'In Progress', 'Paid', 'Pagado'])
            ->get();
            
        return view('admin.appointments.create', compact('patients', 'contractedTreatments'));
    }

    /**
     * Store a newly created appointment manually.
     */
    public function store(Request $request)
    {
        if ($request->has('time')) {
            $request->merge([
                'time' => str_replace(['a. m.', 'p. m.'], ['am', 'pm'], $request->time),
            ]);
        }

        $validated = $request->validate([
            'contracted_treatment_id' => 'required|exists:contracted_treatments,id',
            'date' => 'required|date',
            'time' => 'required|date_format:h:i a',
        ]);

        $schedule = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $contractedTreatment = \App\Models\ContractedTreatment::findOrFail($validated['contracted_treatment_id']);

        $previousAppointmentsCount = Appointment::where('contracted_treatment_id', $contractedTreatment->id)->count();
        $sessionNumber = $previousAppointmentsCount + 1;

        $appointment = Appointment::create([
            'contracted_treatment_id' => $contractedTreatment->id,
            'schedule' => $schedule,
            'session_number' => $sessionNumber,
            'status' => 'Agendado',
        ]);

        try {
            app(NotificationService::class)->sendAppointmentScheduled($appointment);
        } catch (\Throwable $e) {
            \Log::error('Notification error on appointment schedule: ' . $e->getMessage());
        }

        return redirect()->route('admin.appointments.index')->with('success', 'Cita agendada exitosamente.');
    }

    /**
     * Display the appointment management page
     */
    public function index(Request $request)
    {

        $user = auth()->user();
        $branches = $user->hasRole('ADMIN') ? $user->adminsBranches : Branch::all();

        if ($request->has('branch_id')) {
            if ($request->filled('branch_id')) {
                session(['selected_branch_id' => $request->input('branch_id')]);
            } else {
                session()->forget('selected_branch_id');
            }
        }
        $selectedBranchID = session('selected_branch_id', '');
        
        if (!$selectedBranchID && $user->hasRole('ADMIN') && $branches->isNotEmpty()) {
            $selectedBranchID = $branches->first()->id;
            session(['selected_branch_id' => $selectedBranchID]);
        }

        return view('admin.appointments.index', [
            'branches' => $branches,
            'selectedBranchID' => $selectedBranchID,
        ]);
    }

    /**
     * Display the list of appointments that need to be rescheduled.
     */
    public function rescheduleList(): \Illuminate\View\View
    {
        return view('admin.appointments.reschedule-list');
    }

    /**
     * Fetch appointments for a given date range
     */
    public function fetch(Request $request)
    {
        $validated = $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'branch_id' => 'nullable|exists:branches,id',
            'staff_id' => 'nullable|exists:users,id',
            'treatment_id' => 'nullable|exists:treatments,id',
            'status' => 'nullable|string',
            'search' => 'nullable|string|max:255',
        ]);

        $startDate = Carbon::parse($validated['start_date'])->startOfDay();
        $endDate = Carbon::parse($validated['end_date'])->endOfDay();
        
        $branchId = $validated['branch_id'] ?? session('selected_branch_id');
        $user = auth()->user();
        if (empty($branchId) && $user->hasRole('ADMIN')) {
            $branchId = $user->adminsBranches()->first()?->id;
        }

        $query = Appointment::with([
            'contractedTreatment.user',
            'contractedTreatment.treatment',
            'contractedTreatment.branch',
            'staff'
        ])
        ->whereHas('contractedTreatment', function ($q) use ($branchId) {
            if(!empty($branchId)){
                $q->where('branch_id', $branchId);
            }
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

        // Group appointments by date and user_id (all appointments of the same patient on the same day are unified)
        $grouped = $appointments->groupBy(function ($app) {
            return Carbon::parse($app->schedule)->format('Y-m-d') . '_' . $app->contractedTreatment->user_id;
        });

        // Format appointments for frontend
        $formatted = $grouped->map(function ($group) {
            $primaryApp = $group->first();
            $schedule = Carbon::parse($primaryApp->schedule);
            $duration = 20; // Default duration in minutes

            $treatments = $group->map(fn($app) => $app->contractedTreatment->treatment->name)->toArray();

            $subAppointments = $group->map(function ($app) {
                return [
                    'id' => $app->id,
                    'contracted_treatment_id' => $app->contracted_treatment_id,
                    'treatment' => $app->contractedTreatment->treatment->name,
                    'zones' => $app->contractedTreatment->selected_zones,
                    'session_number' => $app->session_number,
                    'status' => $app->status,
                    'attended' => $app->attended,
                    'review' => $app->review,
                    'review_score' => $app->review_score,
                    'shots' => ($app->contractedTreatment->treatment->needs_report_shots && $app->uses_of_hair_removal_shots) ? $app->uses_of_hair_removal_shots : null,
                ];
            })->toArray();

            return [
                'id' => $primaryApp->id,
                'group_ids' => $group->pluck('id')->toArray(),
                'date' => $schedule->format('Y-m-d'),
                'start' => $schedule->format('h:i a'),
                'duration' => $duration,
                'patient' => $primaryApp->contractedTreatment->user->name,
                'branch_id' => $primaryApp->contractedTreatment->branch_id,
                'patient_email' => $primaryApp->contractedTreatment->user->email,
                'professional' => $primaryApp->staff ? $primaryApp->staff->name : 'Sin asignar',
                'treatment' => implode(' + ', $treatments),
                'zones' => $primaryApp->contractedTreatment->selected_zones,
                'status' => $primaryApp->status,
                'attended' => $primaryApp->attended,
                'session_number' => $primaryApp->session_number,
                'review' => $primaryApp->review,
                'review_score' => $primaryApp->review_score,
                'shots' => ($primaryApp->contractedTreatment->treatment->needs_report_shots && $primaryApp->uses_of_hair_removal_shots) ? $primaryApp->uses_of_hair_removal_shots : null,
                'sub_appointments' => $subAppointments,
            ];
        })->values();

        return response()->json(['appointments' => $formatted]);
    }

    /**
     * Get staff list for filters
     */
    public function getStaffList()
    {
        $staff = User::whereHas('staffProfile')->select('id', 'name')->get()->makeVisible('id');

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

        if ($validated['attended']) {
            $scheduleDate = Carbon::parse($appointment->schedule)->startOfDay();
            $today = Carbon::today();

            if ($scheduleDate->gt($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede marcar como atendida una cita futura.'
                ], 422);
            }

            if ($scheduleDate->lt($today) && !$appointment->staff_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para citas pasadas, debe asignar manualmente a la empleada antes de marcarla como atendida.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            // Mark as attended
            $appointment->attended = $validated['attended'];
            $appointment->status = $validated['attended'] ? 'Atendida' : 'No asistida';

            // Assign staff member if not already assigned
            if (!$appointment->staff_user_id && $validated['attended']) {
                $staffId = $this->assignStaffSequentially($appointment);

                if (!$staffId) {
                    DB::rollBack();
                    return response()->json([
                        'success' => false,
                        'message' => 'No hay empleadas libres en este momento. Espere a que una empleada libere su cabina o asigne manualmente.'
                    ], 422);
                }

                $appointment->staff_user_id = $staffId;
                StaffProfile::where('user_id', $staffId)->update([
                    'last_appointment_assigned' => Carbon::now(),
                ]);

                // Group assignment: also assign and mark other appointments for this patient today
                $this->assignGroupAppointments($appointment, $staffId, 'Atendida');
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
        $staffMember = User::select('users.*') // 1. Selecciona solo las columnas de la tabla de usuarios para evitar conflictos.
        ->join('staff_profiles', 'users.id', '=', 'staff_profiles.user_id') // 2. Une la tabla de perfiles.
        ->where('staff_profiles.branch_id', $branchId) // 3. Filtra por branch_id directamente en la tabla unida.
        ->where('users.is_enabled_for_appointments', true)
        ->whereDoesntHave('appointments', function ($q) {
            $q->where('status', 'Atendida')
              ->whereDate('schedule', Carbon::today());
        })
        ->orderBy('staff_profiles.last_appointment_assigned', 'asc') // 4. Ordena por la columna deseada de forma ascendente.
        ->first();

        if ($staffMember) {
            return $staffMember->id;
        }

        return null;
    }

    /**
     * Get all other appointments for the same patient on the same day.
     */
    private function getSameDayAppointments(Appointment $primaryAppointment)
    {
        $patientId = $primaryAppointment->contractedTreatment->user_id;
        $appointmentDate = Carbon::parse($primaryAppointment->schedule)->toDateString();

        return Appointment::whereHas('contractedTreatment', function($query) use ($patientId) {
            $query->where('user_id', $patientId);
        })
        ->whereDate('schedule', $appointmentDate)
        ->where('id', '!=', $primaryAppointment->id)
        ->get();
    }

    /**
     * Finds and updates other appointments for the same patient on the same day,
     * assigning them to the same staff member and marking them as attended.
     */
    private function assignGroupAppointments(Appointment $primaryAppointment, int $staffId, string $status)
    {
        $others = $this->getSameDayAppointments($primaryAppointment);

        foreach($others as $other) {
            $other->staff_user_id = $staffId;
            $other->attended = true;
            $other->status = $status;
            $other->save();
        }
    }

    /**
     * Confirm appointment
     */
    public function confirm(Appointment $appointment)
    {
        $appointment->update([
            'status' => 'Confirmada'
        ]);

        // Cascade: confirm all other appointments for this patient today
        $others = $this->getSameDayAppointments($appointment);
        foreach ($others as $other) {
            $other->update(['status' => 'Confirmada']);
        }

        try {
            app(NotificationService::class)->sendAppointmentConfirmed($appointment);
        } catch (\Throwable $e) {
            \Log::error('Notification error on appointment confirm: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Cita confirmada exitosamente'
        ]);
    }

    /**
     * Reschedule appointment
     */
    public function reschedule(Appointment $appointment, UpdateAppointmentRequest $request)
    {
        $validated = $request->validated();

        $date = $validated['appointment_date'];
        $time = $validated['appointment_time'];
        $schedule = Carbon::parse($date . ' ' . $time);

        // Determine status based on time
        $now = Carbon::now();
        $next24Hours = $now->copy()->addHours(24);
        $status = $schedule->between($now, $next24Hours) ? 'Confirmada' : 'Por confirmar';

        // Cascade: reschedule all other appointments for this patient today
        $others = $this->getSameDayAppointments($appointment);
        foreach ($others as $other) {
            $other->update([
                'schedule' => $schedule->toDateTimeString(),
                'status' => $status,
            ]);
        }

        $appointment->update([
            'schedule' => $schedule->toDateTimeString(),
            'status' => $status,
        ]);

        try {
            app(NotificationService::class)->sendAppointmentScheduled($appointment);
        } catch (\Throwable $e) {
            \Log::error('Notification error on appointment reschedule: ' . $e->getMessage());
        }

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
        try {
            app(NotificationService::class)->sendAppointmentCancelled($appointment);
        } catch (\Throwable $e) {
            \Log::error('Notification error on appointment cancel: ' . $e->getMessage());
        }

        // Cascade: cancel all other appointments for this patient today
        $others = $this->getSameDayAppointments($appointment);
        foreach ($others as $other) {
            $other->delete();
        }

        $appointment->delete();

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

            // Call the method from our trait to get the available slots
            $includeSalesSlots = auth()->check() && auth()->user()->hasAnyRole(['SUPER_ADMIN', 'OWNER', 'ADMIN', 'SALES']);
            $slots = $this->calculateAvailableSlots($date, $branchId, 20, $includeSalesSlots);

            return response()->json(['slots' => $slots]);

        } catch (\Exception $e) {
            // It's good practice to log errors
            \Log::error('Error calculating available slots: ' . $e->getMessage());

            // Return a generic error message to the user
            return response()->json(['error' => 'Could not retrieve available slots.'], 500);
        }
    }

    /**
     * Update the status of an appointment manually.
     */
    public function updateStatus(Appointment $appointment, Request $request)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Por confirmar,Confirmada,Agendado,Atendida,No asistida,Completada',
        ]);

        $status = $validated['status'];

        if ($status === 'Atendida' || $status === 'Completada') {
            $scheduleDate = Carbon::parse($appointment->schedule)->startOfDay();
            $today = Carbon::today();

            if ($scheduleDate->gt($today)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No se puede marcar como atendida o completada una cita futura.'
                ], 422);
            }

            if ($scheduleDate->lt($today) && !$appointment->staff_user_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Para citas pasadas, debe asignar manualmente a la empleada antes de marcarla como atendida.'
                ], 422);
            }
        }

        DB::beginTransaction();
        try {
            $appointment->status = $status;

            if ($status === 'Atendida' || $status === 'Completada') {
                $appointment->attended = true;

                // Assign staff member if not already assigned
                if (!$appointment->staff_user_id) {
                    $staffId = $this->assignStaffSequentially($appointment);

                    if (!$staffId) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'No hay empleadas libres en este momento. Espere a que una empleada libere su cabina o asigne manualmente.'
                        ], 422);
                    }

                    $appointment->staff_user_id = $staffId;
                    StaffProfile::where('user_id', $staffId)->update([
                        'last_appointment_assigned' => Carbon::now(),
                    ]);

                    // Group assignment: also assign and mark other appointments for this patient today
                    $this->assignGroupAppointments($appointment, $staffId, $status);
                }
            } elseif ($status === 'No asistida') {
                $appointment->attended = false;
            } else {
                $appointment->attended = null;
            }

            $appointment->save();

            // Cascade: apply the same status to all other appointments for this patient today
            $others = $this->getSameDayAppointments($appointment);
            foreach ($others as $other) {
                $other->status = $status;
                if ($status === 'Atendida' || $status === 'Completada') {
                    $other->attended = true;
                } elseif ($status === 'No asistida') {
                    $other->attended = false;
                } else {
                    $other->attended = null;
                }
                $other->save();
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Estado de la cita actualizado exitosamente.',
                'appointment' => $appointment->load(['staff', 'contractedTreatment.user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar el estado de la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get real-time status of staff members for the dashboard
     */
    public function getStaffStatus(Request $request)
    {
        $branchId = $request->input('branch_id');
        $user = auth()->user();
        
        if (!$branchId && $user->hasRole('ADMIN')) {
            $branchId = $user->adminsBranches()->first()?->id;
        }

        if (!$branchId && $user->hasRole('ADMIN')) {
            return response()->json(['staff' => []]);
        }

        // Get all staff for this branch (or all branches if empty and user is not just ADMIN)
        $staffMembers = User::whereHas('staffProfile', function($q) use ($branchId) {
            if ($branchId) {
                $q->where('branch_id', $branchId);
            }
        })->get();

        $statusList = [];

        foreach ($staffMembers as $staff) {
            // Check if they have an active appointment right now (Atendida today)
            $activeAppointment = Appointment::with(['contractedTreatment.user', 'contractedTreatment.treatment'])
                ->where('staff_user_id', $staff->id)
                ->where('status', 'Atendida')
                ->whereDate('schedule', Carbon::today())
                ->first();

            if ($activeAppointment) {
                $statusList[] = [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'status' => 'Ocupada',
                    'patient' => $activeAppointment->contractedTreatment->user->name,
                    'treatment' => $activeAppointment->contractedTreatment->treatment->name,
                    'entry_time' => $activeAppointment->updated_at->format('h:i a'),
                ];
            } else {
                $statusList[] = [
                    'id' => $staff->id,
                    'name' => $staff->name,
                    'status' => 'Libre',
                    'patient' => null,
                    'treatment' => null,
                    'entry_time' => null,
                ];
            }
        }

        return response()->json(['staff' => $statusList]);
    }

    /**
     * Manually reassign a staff member to an appointment
     */
    public function reassignStaff(Appointment $appointment, Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
        ]);

        DB::beginTransaction();
        try {
            $staffId = $validated['staff_id'];
            $appointment->staff_user_id = $staffId;
            $appointment->save();

            // Update last_appointment_assigned for round-robin consistency
            StaffProfile::where('user_id', $staffId)->update([
                'last_appointment_assigned' => Carbon::now(),
            ]);

            // Cascade to other same-day appointments
            $others = $this->getSameDayAppointments($appointment);
            foreach ($others as $other) {
                $other->staff_user_id = $staffId;
                $other->save();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Cita reasignada exitosamente.',
                'appointment' => $appointment->load(['staff', 'contractedTreatment.user'])
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al reasignar la cita: ' . $e->getMessage()
            ], 500);
        }
    }
}

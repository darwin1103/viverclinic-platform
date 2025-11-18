<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreAppointmentRequest;
use App\Http\Requests\Client\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\ContractedTreatment;
use App\Traits\CalculatesAvailableSlots;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleAppointmentController extends Controller
{
    use CalculatesAvailableSlots;

    /**
     * Display the appointment scheduling page
     */
    public function index(ContractedTreatment $contracted_treatment)
    {

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        // Check payment status ***

        $contracted_treatment->load(['branch', 'treatment', 'appointments']);
        $paymentIsUpToDate = true;

        $contracted_treatment->appointments->each(function($appointment){
            $appointment->date = Carbon::parse($appointment->schedule)->isoFormat('YYYY-MM-DD');
            $appointment->time = Carbon::parse($appointment->schedule)->isoFormat('hh:mm a');
        });

        // Calculate statistics
        $attendedCount = $contracted_treatment->appointments->where('attended', true)->count();
        $missedCount = $contracted_treatment->appointments->whereStrict('attended', false)->count();
        $pendingCount = $contracted_treatment->sessions - ($attendedCount + $missedCount);

        Carbon::setLocale('es');

        return view('client.schedule-appointment.index', [
            'contracted_treatment' => $contracted_treatment,
            'paymentIsUpToDate' => $paymentIsUpToDate,
            'totalSessions' => $contracted_treatment->sessions,
            'sessions' => $contracted_treatment->appointments,
            'attendedCount' => $attendedCount,
            'missedCount' => $missedCount,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Store a new appointment
     */
    public function store(StoreAppointmentRequest $request)
    {

        // check appointment_date and appointment_time are available ***
        // Check payment status ***

        $validated = $request->validated();

        $date = $validated['appointment_date'];
        $time = $validated['appointment_time'];

        $now = Carbon::now();
        $next24Hours = $now->copy()->addHours(24);
        $schedule = Carbon::parse($date . ' ' . $time);

        $status = ($schedule->between($now, $next24Hours)) ? 'Confirmada' : 'Por confirmar';

        $contractedTreatmentId = $validated['contracted_treatment_id'];

        $contracted_treatment = ContractedTreatment::where('id', $contractedTreatmentId)
            ->select(['id', 'user_id'])
            ->first();

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        Appointment::create([
            'contracted_treatment_id' => $contractedTreatmentId,
            'schedule' => $schedule->toDateTimeString(),
            'session_number' => $validated['session_number'],
            'status' => $status,
        ]);

        return redirect()
            ->route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', 'Cita agendada exitosamente');
    }

    /**
     * Store a new appointment
     */
    public function resched(Appointment $appointment, UpdateAppointmentRequest $request)
    {

        // check appointment_date and appointment_time are available ***
        // Check payment status ***

        $validated = $request->validated();

        $date = $validated['appointment_date'];
        $time = $validated['appointment_time'];

        $now = Carbon::now();
        $next24Hours = $now->copy()->addHours(24);
        $schedule = Carbon::parse($date . ' ' . $time);

        $status = ($schedule->between($now, $next24Hours)) ? 'Confirmada' : 'Por confirmar';

        $contractedTreatmentId = $appointment->contracted_treatment_id;

        $contracted_treatment = ContractedTreatment::where('id', $contractedTreatmentId)
            ->select(['id', 'user_id'])
            ->first();

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        $appointment->update([
            'schedule' => $schedule->toDateTimeString(),
            'status' => $status,
        ]);

        return redirect()
            ->route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', 'Cita re-agendada exitosamente');
    }

    /**
     * Rate a completed session
     */
    public function rate(Appointment $appointment, Request $request)
    {

        // verify if the user is the owner of contracted treatment ***
        // Check payment status ***

        $validated = $request->validate([
            'rating_value' => 'required|integer|min:1|max:3',
            'comment' => 'nullable|string|max:500',
        ]);

        if($appointment->status != 'Atendida'){
            abort(403);
        }

        $contractedTreatmentId = $appointment->contracted_treatment_id;

        $contracted_treatment = ContractedTreatment::where('id', $contractedTreatmentId)
            ->select(['id', 'user_id'])
            ->first();

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        $appointment->update([
            'review' => $validated['comment'],
            'review_score' => $validated['rating_value'],
        ]);

        return redirect()
            ->route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', '¡Gracias por tu calificación!');
    }

    /**
     * Confirm a scheduled appointment (mark as attended)
     */
    public function confirm(Appointment $appointment)
    {

        // Check payment status ***

        $contractedTreatmentId = $appointment->contracted_treatment_id;

        $contracted_treatment = ContractedTreatment::where('id', $contractedTreatmentId)
            ->select(['id', 'user_id'])
            ->first();

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        $appointment->update([
            'status' => 'Confirmada',
        ]);

        return redirect()
            ->route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', 'Cita confirmada como asistida');
    }

    /**
     * Cancel a scheduled appointment
     */
    public function cancel(Appointment $appointment)
    {

        $contractedTreatmentId = $appointment->contracted_treatment_id;

        $contracted_treatment = ContractedTreatment::where('id', $contractedTreatmentId)
            ->select(['id', 'user_id'])
            ->first();

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        // Check payment status ***

        $appointment->delete();

        return redirect()
            ->route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', 'Cita cancelada exitosamente');
    }

    /**
     * Find available appointment slots based on a given date and branch.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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
            // You can also pass custom values for slot duration and additional capacity if needed
            $slots = $this->calculateAvailableSlots($date, $branchId);

            return response()->json(['slots' => $slots]);

        } catch (\Exception $e) {
            // It's good practice to log errors
            \Log::error('Error calculating available slots: ' . $e->getMessage());

            // Return a generic error message to the user
            return response()->json(['error' => 'Could not retrieve available slots.'], 500);
        }
    }
}

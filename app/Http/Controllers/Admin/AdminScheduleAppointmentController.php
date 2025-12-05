<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreAppointmentRequest;
use App\Http\Requests\Client\UpdateAppointmentRequest;
use App\Models\Appointment;
use App\Models\ContractedTreatment;
use App\Traits\CalculatesAvailableSlots;
use App\Traits\ValidatesAppointmentSlot;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminScheduleAppointmentController extends Controller
{
    use CalculatesAvailableSlots;
    use ValidatesAppointmentSlot;

    /**
     * Display the appointment scheduling page
     */
    public function index(ContractedTreatment $contracted_treatment)
    {

        $contracted_treatment->load(['branch', 'treatment', 'appointments', 'user']);
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

        return view('admin.schedule-appointment.index', [
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

        // Check payment status ***

        $validated = $request->validated();

        // Obtener la información necesaria para la validación
        $contractedTreatment = ContractedTreatment::findOrFail($validated['contracted_treatment_id']);
        $branchId = $contractedTreatment->branch_id;

        $date = Carbon::parse($validated['appointment_date']);
        // Convertir el tiempo de formato 'h:i a' a 'H:i' (24h) que el Trait necesita
        $time24h = Carbon::createFromFormat('h:i a', $validated['appointment_time'])->format('H:i');

        // 3. Realizar la validación de disponibilidad usando el Trait
        $isAvailable = $this->isSlotAvailable($date, $time24h, $branchId);

        if (!$isAvailable) {
            // Si no está disponible, redirigir al usuario de vuelta al formulario
            // con un error de validación específico.
            return back()
                ->with(['error' => 'El horario seleccionado ya no se encuentra disponible. Por favor, elija otro.'])
                ->withInput();
        }

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

        Appointment::create([
            'contracted_treatment_id' => $contractedTreatmentId,
            'schedule' => $schedule->toDateTimeString(),
            'session_number' => $validated['session_number'],
            'status' => $status,
        ]);

        return redirect()
            ->route('admin.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', 'Cita agendada exitosamente');
    }

    /**
     * Store a new appointment
     */
    public function resched(Appointment $appointment, UpdateAppointmentRequest $request)
    {

        // Check payment status ***

        $validated = $request->validated();

        // Obtener la información necesaria para la validación
        $contractedTreatment = ContractedTreatment::findOrFail($appointment->contracted_treatment_id);
        $branchId = $contractedTreatment->branch_id;

        $date = Carbon::parse($validated['appointment_date']);
        // Convertir el tiempo de formato 'h:i a' a 'H:i' (24h) que el Trait necesita
        $time24h = Carbon::createFromFormat('h:i a', $validated['appointment_time'])->format('H:i');

        // 3. Realizar la validación de disponibilidad usando el Trait
        $isAvailable = $this->isSlotAvailable($date, $time24h, $branchId);

        if (!$isAvailable) {
            // Si no está disponible, redirigir al usuario de vuelta al formulario
            // con un error de validación específico.
            return back()
                ->with(['error' => 'El horario seleccionado ya no se encuentra disponible. Por favor, elija otro.'])
                ->withInput();
        }

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

        $appointment->update([
            'schedule' => $schedule->toDateTimeString(),
            'status' => $status,
        ]);

        return redirect()
            ->route('admin.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
            ->with('success', 'Cita re-agendada exitosamente');
    }

    /**
     * Rate a completed session
     */
    public function rate(Appointment $appointment, Request $request)
    {

        // verify if the user is the owner of contracted treatment ***
        // Check payment status ***

        $messages = [
            'rating_value.required' => 'La puntuación es obligatoria.',
            'rating_value.integer'  => 'La puntuación debe ser un número entero.',
            'rating_value.min'      => 'La puntuación mínima permitida es :min.',
            'rating_value.max'      => 'La puntuación máxima permitida es :max.',
            'comment.string'        => 'El comentario debe ser texto.',
            'comment.max'           => 'El comentario no debe exceder los :max caracteres.',
        ];

        $attributes = [
            'rating_value' => 'puntuación',
            'comment'      => 'comentario',
        ];

        $validated = $request->validate([
            'rating_value' => 'required|integer|min:1|max:3',
            'comment'      => 'nullable|string|max:500',
        ], $messages, $attributes);

        if(
            $appointment->status != 'Atendida' &&
            $appointment->status != 'Completada'
        ){
            abort(403);
        }

        $contractedTreatmentId = $appointment->contracted_treatment_id;

        $contracted_treatment = ContractedTreatment::where('id', $contractedTreatmentId)
            ->select(['id', 'user_id'])
            ->first();

        $appointment->update([
            'review' => $validated['comment'],
            'review_score' => $validated['rating_value'],
        ]);

        return redirect()
            ->route('admin.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
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

        $appointment->update([
            'status' => 'Confirmada',
        ]);

        return redirect()
            ->route('admin.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
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

        // Check payment status ***

        $appointment->delete();

        return redirect()
            ->route('admin.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
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
         $messages = [
            'date.required'           => 'La fecha de la reserva es obligatoria.',
            'date.date_format'        => 'La fecha debe tener el formato AAAA-MM-DD (ej: 2025-12-31).',
            'date.after_or_equal'     => 'La fecha de la reserva no puede ser anterior al día de hoy.',

            'branch_id.required'      => 'El campo de la sucursal es obligatorio.',
            'branch_id.integer'       => 'El ID de la sucursal debe ser un número entero.',
            'branch_id.exists'        => 'La sucursal seleccionada no es válida o no existe.',
        ];

        $attributes = [
            'date'      => 'fecha de la reserva',
            'branch_id' => 'sucursal',
        ];

        $validated = $request->validate([
            'date'      => 'required|date_format:Y-m-d|after_or_equal:today',
            'branch_id' => 'required|integer|exists:branches,id',
        ], $messages, $attributes);

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

<?php

namespace App\Http\Controllers\Client;

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
use App\Services\NotificationService;

class ScheduleAppointmentController extends Controller
{
    use CalculatesAvailableSlots;
    use ValidatesAppointmentSlot;

    /**
     * Display the appointment scheduling page
     */
    public function index(ContractedTreatment $contracted_treatment)
    {

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        $contracted_treatment->load(['branch', 'treatment', 'appointments']);

        $contracted_treatment->appointments->each(function($appointment){
            $appointment->date = Carbon::parse($appointment->schedule)->isoFormat('YYYY-MM-DD');
            $appointment->time = Carbon::parse($appointment->schedule)->isoFormat('hh:mm a');
        });

        // Calculate statistics
        $attendedCount = $contracted_treatment->appointments->where('attended', true)->count();
        $missedCount = $contracted_treatment->appointments->whereStrict('attended', false)->count();
        $pendingCount = $contracted_treatment->sessions - ($attendedCount + $missedCount);

        Carbon::setLocale('es');

        // --- LÓGICA DE VALIDACIÓN DE PAGOS ---

        // Determinar cuál es la próxima sesión (secuencial)
        $lastCompletedOrMissed = $contracted_treatment->appointments->filter(function($appointment) {
            return $appointment->attended !== null || $appointment->status === 'No asistida';
        })->max('session_number') ?? 0;
        $nextSessionNumber = $lastCompletedOrMissed + 1;

        // Verificar si existe una cita futura ya agendada (pendiente de asistencia)
        $hasFutureAppointment = $contracted_treatment->appointments->filter(function($appointment) {
            return is_null($appointment->attended) && !in_array($appointment->status, ['No asistida', 'Cancelada']);
        })->count() > 0;

        $paymentIsUpToDate = false;
        $nextPaymentAmount = 0;
        $nextPaymentDescription = '';
        $canPayInstallment = false;

        $totalRemainingAmount = 0;

        if ($contracted_treatment->isFullyPaid()) {
            // Si está pagado totalmente, todo está al día
            $paymentIsUpToDate = true;
            $totalRemainingAmount = 0;
        } else {

            // Si es abono
            if ($contracted_treatment->payment_type === 'abono') {
                $paymentIsUpToDate = false;
                $totalRemainingAmount = $contracted_treatment->remainingBalance();
                $minAbono = (int) \App\Models\Setting::get('minimum_abono_amount', '50000');
                $nextPaymentAmount = min($minAbono, $totalRemainingAmount);
                $nextPaymentDescription = "Abono Mínimo";
                $canPayInstallment = false;
            }
            // Si tiene cuotas definidas
            elseif ($contracted_treatment->hasInstallments()) {

                // Buscar la cuota correspondiente a la próxima sesión
                // Regla: Para agendar sesión N, la cuota N debe estar pagada.
                // Si N > TotalCuotas, todas las cuotas deben estar pagadas.

                $installments = $contracted_treatment->installments->sortBy('installment_number');
                $totalInstallments = $installments->count();

                // Identificamos qué cuota bloquea el proceso
                $targetInstallmentNumber = ($nextSessionNumber > $totalInstallments) ? $totalInstallments : $nextSessionNumber;

                // Verificar si esa cuota (y las anteriores) están pagadas
                // Ignoramos cuotas con precio 0 ya que no bloquean el proceso
                $pendingInstallment = $installments->where('status', 'PENDING')
                                                   ->where('installment_number', '<=', $targetInstallmentNumber)
                                                   ->where('price', '>', 0)
                                                   ->first();

                if (!$pendingInstallment) {
                    // No hay cuotas pendientes que bloqueen la siguiente sesión
                    $paymentIsUpToDate = true;
                } else {
                    $paymentIsUpToDate = false;
                    $nextPaymentAmount = $pendingInstallment->price;
                    $nextPaymentDescription = "Cuota #{$pendingInstallment->installment_number}";
                    $canPayInstallment = true; // Habilita botón de pagar cuota individual
                }

                // Sumar todas las cuotas que estén pendientes
                $totalRemainingAmount = $contracted_treatment->installments
                    ->where('status', 'PENDING')
                    ->sum('price');

            } else {
                // No tiene cuotas, debe pagar el total
                $paymentIsUpToDate = false; // Porque status no es 'Paid'
                $totalRemainingAmount = $contracted_treatment->remainingBalance();
                $nextPaymentAmount = $totalRemainingAmount;
                $nextPaymentDescription = "Pago Total del Tratamiento";
                $canPayInstallment = false; // Solo pago total
            }
        }

        // --- Preparar datos para la vista ---

        // Calcular estadísticas...
        $attendedCount = $contracted_treatment->appointments->where('attended', true)->count();
        $missedCount = $contracted_treatment->appointments->whereStrict('attended', false)->count(); // Strict false check
        $pendingCount = $contracted_treatment->sessions - ($attendedCount + $missedCount);

        // Formatear fechas...
        $contracted_treatment->appointments->each(function($appointment){
             $appointment->date = Carbon::parse($appointment->schedule)->isoFormat('YYYY-MM-DD');
             $appointment->time = Carbon::parse($appointment->schedule)->isoFormat('hh:mm a');
        });

        $paymentVerificationPending = $contracted_treatment->orders()
            ->where('status', 'Pago por verificar') // Estado usado para Transferencia/Efectivo
            ->exists();

        $lastOrder = $contracted_treatment->orders()->latest()->first();
        $lastOrderRejected = ($lastOrder && $lastOrder->status === 'Cancelado');

        return view('client.schedule-appointment.index', [
            'contracted_treatment' => $contracted_treatment,
            'paymentIsUpToDate' => $paymentIsUpToDate,
            'nextPaymentAmount' => $nextPaymentAmount,
            'nextPaymentDescription' => $nextPaymentDescription,
            'canPayInstallment' => $canPayInstallment,
            'nextSessionNumber' => $nextSessionNumber,
            'hasFutureAppointment' => $hasFutureAppointment,
            'totalSessions' => $contracted_treatment->sessions,
            'sessions' => $contracted_treatment->appointments,
            'attendedCount' => $attendedCount,
            'missedCount' => $missedCount,
            'pendingCount' => $pendingCount,
            'totalRemainingAmount' => $totalRemainingAmount,
            'paymentVerificationPending' => $paymentVerificationPending,
            'lastOrderRejected' => $lastOrderRejected,
            'lastOrderMessage' => $lastOrder ? $lastOrder->payment_description : ''
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

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        $appointment = Appointment::create([
            'contracted_treatment_id' => $contractedTreatmentId,
            'schedule' => $schedule->toDateTimeString(),
            'session_number' => $validated['session_number'],
            'status' => $status,
        ]);

        app(NotificationService::class)->sendAppointmentScheduled($appointment);

        return redirect()
            ->route('client.schedule-appointment.index', ['contracted_treatment' => $contractedTreatmentId])
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

        $user = Auth::user();

        if($contracted_treatment->user_id != $user->id){
            abort(403);
        }

        $appointment->update([
            'schedule' => $schedule->toDateTimeString(),
            'status' => $status,
        ]);

        app(NotificationService::class)->sendAppointmentScheduled($appointment);

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

        $messages = [
            // Reglas para 'rating_value'
            'rating_value.required' => 'La puntuación es obligatoria.',
            'rating_value.integer'  => 'La puntuación debe ser un número entero.',
            'rating_value.min'      => 'La puntuación mínima permitida es :min.', // O usar '1' directamente
            'rating_value.max'      => 'La puntuación máxima permitida es :max.', // O usar '3' directamente

            // Reglas para 'comment'
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

        app(NotificationService::class)->sendAppointmentConfirmed($appointment);

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

        app(NotificationService::class)->sendAppointmentCancelled($appointment);

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
        $messages = [
            // Reglas para 'date'
            'date.required'           => 'La fecha es obligatoria.',
            'date.date_format'        => 'La fecha debe tener el formato AAAA-MM-DD (ej: 2025-12-31).',
            'date.after_or_equal'     => 'La fecha no puede ser anterior al día de hoy.',

            // Reglas para 'branch_id'
            'branch_id.required'      => 'El campo de la sucursal es obligatorio.',
            'branch_id.integer'       => 'El ID de la sucursal debe ser un número entero.',
            'branch_id.exists'        => 'La sucursal seleccionada no es válida o no existe.',
        ];

        $attributes = [
            'date'      => 'Fecha',
            'branch_id' => 'Sucursal',
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

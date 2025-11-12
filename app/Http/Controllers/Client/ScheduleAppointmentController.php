<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use App\Traits\CalculatesAvailableSlots;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleAppointmentController extends Controller
{
    use CalculatesAvailableSlots;

    /**
     * Display the appointment scheduling page
     */
    public function index(ContractedTreatment $contracted_treatment)
    {

        // validate user is owner of the contrated_treatment ***

        $contracted_treatment->load(['branch', 'treatment']);
// dd($contracted_treatment);

        // Check payment status
        $paymentIsUpToDate = true;

        // Sessions data - This should come from database
        // Each session should have: session_number, date, time, attended (bool|null), review_score (int|null)
        $sessionsData = [
            [
                'session_number' => 1,
                'date' => '2025-10-05',
                'time' => '09:00',
                'attended' => true,
                'review_score' => 3
            ],
            [
                'session_number' => 2,
                'date' => '2025-10-06',
                'time' => '09:40',
                'attended' => false,
                'review_score' => null
            ],
            [
                'session_number' => 3,
                'date' => '2025-10-07',
                'time' => '11:40',
                'attended' => true,
                'review_score' => null // Not rated yet
            ],
            // [
            //     'session_number' => 4,
            //     'date' => '2025-11-15',
            //     'time' => '10:00',
            //     'attended' => null, // Future appointment
            //     'review_score' => null
            // ],
            // Sessions 5-10 have no date yet (not scheduled)
        ];

        $sessions = collect($sessionsData);

        // Calculate statistics
        $attendedCount = $sessions->where('attended', true)->count();
        $missedCount = $sessions->whereStrict('attended', false)->count();
        $pendingCount = $contracted_treatment->sessions - ($attendedCount + $missedCount);

        Carbon::setLocale('es');

        return view('client.schedule-appointment.index', [
            'contracted_treatment' => $contracted_treatment,
            'paymentIsUpToDate' => $paymentIsUpToDate,
            'totalSessions' => $contracted_treatment->sessions,
            'sessions' => $sessions,
            'attendedCount' => $attendedCount,
            'missedCount' => $missedCount,
            'pendingCount' => $pendingCount,
        ]);
    }

    /**
     * Store a new appointment
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'session_number' => 'required|integer|min:1',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|date_format:H:i',
        ]);

        // TODO: Save appointment to database
        // Example:
        // Appointment::updateOrCreate(
        //     [
        //         'treatment_id' => $treatmentId,
        //         'session_number' => $validated['session_number']
        //     ],
        //     [
        //         'date' => $validated['appointment_date'],
        //         'time' => $validated['appointment_time'],
        //         'specialist_id' => $specialistId,
        //         'branch_id' => $branchId,
        //         'status' => 'scheduled'
        //     ]
        // );

        return redirect()
            ->route('schedule-appointment.index')
            ->with('success', 'Cita agendada exitosamente');
    }

    /**
     * Rate a completed session
     */
    public function rate(Request $request)
    {
        $validated = $request->validate([
            'session_number' => 'required|integer|min:1',
            'rating_value' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500',
        ]);

        // TODO: Save rating to database
        // Example:
        // Appointment::where('treatment_id', $treatmentId)
        //     ->where('session_number', $validated['session_number'])
        //     ->update([
        //         'review_score' => $validated['rating_value'],
        //         'review_comment' => $validated['comment']
        //     ]);

        return redirect()
            ->route('schedule-appointment.index')
            ->with('success', '¡Gracias por tu calificación!');
    }

    /**
     * Confirm a scheduled appointment (mark as attended)
     */
    public function confirm($sessionNumber)
    {
        // TODO: Update appointment status
        // Example:
        // Appointment::where('treatment_id', $treatmentId)
        //     ->where('session_number', $sessionNumber)
        //     ->update([
        //         'attended' => true,
        //         'confirmed_at' => now()
        //     ]);

        return redirect()
            ->route('schedule-appointment.index')
            ->with('success', 'Cita confirmada como asistida');
    }

    /**
     * Cancel a scheduled appointment
     */
    public function cancel($sessionNumber)
    {
        // TODO: Update appointment status
        // Example:
        // Appointment::where('treatment_id', $treatmentId)
        //     ->where('session_number', $sessionNumber)
        //     ->update([
        //         'date' => null,
        //         'time' => null,
        //         'status' => 'cancelled',
        //         'cancelled_at' => now()
        //     ]);

        return redirect()
            ->route('schedule-appointment.index')
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
\Log::info(print_r($slots,true));
            return response()->json(['slots' => $slots]);

        } catch (\Exception $e) {
            // It's good practice to log errors
            \Log::error('Error calculating available slots: ' . $e->getMessage());

            // Return a generic error message to the user
            return response()->json(['error' => 'Could not retrieve available slots.'], 500);
        }
    }
}

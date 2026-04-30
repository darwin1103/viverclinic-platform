<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CancelAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $nextAppointment = \App\Models\Appointment::with(['contractedTreatment.treatment', 'contractedTreatment.branch'])
            ->whereHas('contractedTreatment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('schedule', '>=', now())
            ->whereIn('status', ['Agendado', 'Confirmado', 'Pendiente', 'Pending', 'Scheduled', 'Confirmed', 'Por confirmar'])
            ->orderBy('schedule', 'asc')
            ->first();

        return view('cancel-appointment.index', compact('nextAppointment'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $appointment = \App\Models\Appointment::findOrFail($id);

        // Verify ownership
        $user = auth()->user();
        $contractedTreatment = $appointment->contractedTreatment;
        if ($contractedTreatment->user_id !== $user->id) {
            abort(403);
        }

        // Cancel logic: We delete the appointment or change status.
        // The business logic says "devuelva o libere esa sesión". As analyzed, deleting the appointment 
        // completely removes it from the 'attended'/'missed' count, thereby freeing the pending session count.
        $appointment->delete();

        return redirect()->route('dashboard')
            ->with('success', 'Cita cancelada exitosamente. Tu sesión ha sido devuelta a tu paquete.');
    }
}

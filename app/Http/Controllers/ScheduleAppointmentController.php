<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ScheduleAppointmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

        $treatment = (object)['id' => 1, 'name' => 'Tratamiento de Depilación'];
        $branch = (object)['id' => 1, 'name' => 'Sucursal Centro', 'tlf' => '+123456789', 'google_maps_link' => 'https://www.google.com/maps/@48.8458952,2.2850176,11z?entry=ttu&g_ep=EgoyMDI1MTAyMC4wIKXMDSoASAFQAw%3D%3D'];
        $specialist = (object)['id' => 1, 'name' => 'Dra. Ana'];
        $paymentIsUpToDate = true;
        $totalSessionsInTreatment = 10;

        $sessionsData = [
            ['session_number' => 1, 'date' => '2025-10-05', 'attended' => true],
            ['session_number' => 2, 'date' => '2025-10-06', 'attended' => false],
            ['session_number' => 3, 'date' => '2025-10-07', 'attended' => true],
            ['session_number' => 4, 'date' => null, 'attended' => null],

        ];
        $sessions = collect($sessionsData);

        $attendedCount = $sessions->where('attended', true)->count();
        $missedCount = $sessions->where('attended', false)->count();
        $pendingCount = $totalSessionsInTreatment - ($attendedCount + $missedCount);

        return view('schedule-appointment.index', [
            'treatment' => $treatment,
            'branch' => $branch,
            'specialist' => $specialist,
            'paymentIsUpToDate' => $paymentIsUpToDate,
            'totalSessions' => $totalSessionsInTreatment,
            'sessions' => $sessions,
            'attendedCount' => $attendedCount,
            'missedCount' => $missedCount,
            'pendingCount' => $pendingCount,
        ]);
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
        //
    }
}

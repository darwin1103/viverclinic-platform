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
        return view('schedule-appointment.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {



        $packages = [
            [
                'id' => 1,
                'name' => 'Paquete 1',
                'big_zones' => 1,
                'mini_zones' => 2,
                'price' => 149999
            ],
            [
                'id' => 2,
                'name' => 'Paquete 2',
                'big_zones' => 2,
                'mini_zones' => 2,
                'price' => 249999
            ],
            [
                'id' => 3,
                'name' => 'Paquete 3',
                'big_zones' => 4,
                'mini_zones' => 2,
                'price' => 349999
            ],
        ];

        $additionalZones = [
            [
                'id' => 'mini',
                'name' => '10 sesiones mini zona',
                'price' => 59999
            ],
            [
                'id' => 'grande',
                'name' => '10 sesiones una zona',
                'price' => 129999
            ],
        ];

        $bigZones = [
            'Muslo', 'Media pierna', 'Glúteos', 'Abdomen', 'Pecho', 'Brazos', 'Espalda Alta', 'Espalda Baja'
        ];

        $smallZones = [
            'Bikini', 'Axilas', 'Facial o Barba', 'Cuello', 'Linea completa Abdomen'
        ];

        $miniZones = [
            'Vellos de los dedos pies', 'Vellos de los dedos mano', 'Empeine', 'Perianal', 'Bigote', 'Patillas', 'Barbilla', 'Orejas', 'Entre cejo', 'Linea alba', 'Pezones', 'Marcación barba'
        ];




        return view('schedule-appointment.create', compact('packages', 'additionalZones', 'bigZones', 'smallZones', 'miniZones'));
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

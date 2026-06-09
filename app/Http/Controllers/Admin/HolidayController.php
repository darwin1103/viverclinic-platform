<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;

class HolidayController extends Controller
{
    /**
     * Store a new holiday.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:100',
        ], [
            'date.required' => 'La fecha es obligatoria.',
            'date.date' => 'Ingresa una fecha válida.',
            'date.unique' => 'Ya existe un festivo registrado para esa fecha.',
            'name.required' => 'El nombre del festivo es obligatorio.',
            'name.max' => 'El nombre no puede exceder los 100 caracteres.',
        ]);

        Holiday::create([
            'date' => $request->date,
            'name' => $request->name,
        ]);

        return back()->with('success', 'Día festivo agregado correctamente.');
    }

    /**
     * Remove a holiday.
     */
    public function destroy(Holiday $holiday): \Illuminate\Http\RedirectResponse
    {
        $holiday->delete();

        return back()->with('success', 'Día festivo eliminado correctamente.');
    }
}

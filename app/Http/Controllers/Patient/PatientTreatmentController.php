<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientTreatmentController extends Controller
{
    /**
     * Muestra los tratamientos disponibles para una sucursal específica.
     */
    public function show(Branch $branch)
    {
        // Cargar solo los tratamientos activos asociados a esta sucursal
        $treatments = $branch->treatments()->where('active', true)->get();
        return view('patient.treatment.show', compact('branch', 'treatments'));
    }

    /**
     * Almacena la selección de tratamiento del paciente.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $branch = Branch::findOrFail($validated['branch_id']);
        $treatment = $branch->treatments()->findOrFail($validated['treatment_id']);

        // El precio se obtiene de la tabla pivote para seguridad
        $price = $treatment->pivot->price;

        ContractedTreatment::create([
            'user_id' => Auth::id(),
            'branch_id' => $branch->id,
            'treatment_id' => $treatment->id,
            'price' => $price,
            'status' => 'pending', // Estado inicial
        ]);

        // Redirigir a una página de confirmación o de pago
        return redirect()->route('home')->with('success', '¡Has seleccionado tu tratamiento! El siguiente paso es agendar tu cita.');
    }
}

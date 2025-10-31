<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientBuyTreatmentController extends Controller
{
    /**
     * Muestra los tratamientos disponibles para una sucursal específica.
     */
    public function index()
    {
        // Cargar solo los tratamientos activos asociados a esta sucursal
        $user = Auth::user();
        $branch = $user->patientProfile->branch;
        $treatments = $branch->treatments()->where('active', true)->get();
        return view('patient.treatment.index', compact('branch', 'treatments'));
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
        return redirect()->route('buy-package.create')->with('success', '¡Has seleccionado tu tratamiento! El siguiente paso es agendar tu cita.');
    }
}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyTreatmentController extends Controller
{
    /**
     * Muestra los tratamientos disponibles para una sucursal específica.
     */
    public function index()
    {
        // Cargar solo los tratamientos activos asociados a esta sucursal
        $user = Auth::user();
        $branch = $user->patientProfile->branch;
        $treatments = Treatment::where('active', true)
            ->whereHas('packages', function ($query) use ($branch) {
                $query->where('branch_id', $branch->id);
            })
            ->get();
        return view('client.treatment.index', compact('branch', 'treatments'));
    }


    public function show(Treatment $treatment)
    {

        $user = Auth::user();
        $branch = $user->patientProfile->branch;

        $isAvailableInBranch = $treatment->packages()
            ->where('branch_id', $branch->id)
            ->exists();

        if (!$isAvailableInBranch) {
            abort(404);
        }

        $packages = $treatment->packages()
            ->where('branch_id', $branch->id)
            ->select('id', 'name', 'price', 'big_zones', 'mini_zones')
            ->get();

        $additionalZones = [
            [
                'id' => 'mini',
                'name' => '10 sesiones una mini zona',
                'price' => $treatment->price_additional_zone,
            ],
            [
                'id' => 'grande',
                'name' => '10 sesiones una zona',
                'price' => $treatment->price_additional_mini_zone,
            ],
        ];

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        return view('client.treatment.show', compact('packages', 'additionalZones', 'bigZones', 'smallZones', 'miniZones'));

    }




    /**
     * Almacena la selección de tratamiento del paciente.
     */
    // public function store(Request $request)
    // {
    //     $validated = $request->validate([
    //         'treatment_id' => 'required|exists:treatments,id',
    //         'branch_id' => 'required|exists:branches,id',
    //     ]);

    //     $branch = Branch::findOrFail($validated['branch_id']);
    //     $treatment = $branch->treatments()->findOrFail($validated['treatment_id']);

    //     // El precio se obtiene de la tabla pivote para seguridad
    //     $price = $treatment->pivot->price;

    //     ContractedTreatment::create([
    //         'user_id' => Auth::id(),
    //         'branch_id' => $branch->id,
    //         'treatment_id' => $treatment->id,
    //         'price' => $price,
    //         'status' => 'pending', // Estado inicial
    //     ]);

    //     // Redirigir a una página de confirmación o de pago
    //     return redirect()->route('buy-package.create')->with('success', '¡Has seleccionado tu tratamiento! El siguiente paso es agendar tu cita.');
    // }
}

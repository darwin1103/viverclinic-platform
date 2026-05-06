<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Http\Request;

class LegacyTreatmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('role:SUPER_ADMIN|OWNER');
    }

    public function create()
    {
        // Solo usuarios antiguos (is_legacy = true) con rol paciente
        $legacyUsers = User::role('PATIENT')->where('is_legacy', true)->get();
        $treatments = Treatment::where('active', true)->get();
        $branches = Branch::all();

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        return view('admin.legacy-treatments.create', compact(
            'legacyUsers', 'treatments', 'branches', 
            'bigZones', 'smallZones', 'miniZones'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'treatment_id' => 'required|exists:treatments,id',
            'branch_id' => 'required|exists:branches,id',
            'sessions' => 'required|integer|min:1',
            'selected_zones' => 'nullable|array',
            'another_big_zone' => 'nullable|string',
            'another_mini_zone' => 'nullable|string',
        ]);

        // Aseguramos que el usuario es legacy
        $user = User::findOrFail($request->user_id);
        if (!$user->is_legacy) {
            return back()->with('error', 'El usuario seleccionado no está marcado como usuario antiguo.');
        }

        $treatment = Treatment::findOrFail($request->treatment_id);

        $selectedZones = $request->selected_zones ?? ['big' => [], 'mini' => []];
        if (!empty($request->another_big_zone)) $selectedZones['big'][] = $request->another_big_zone;
        if (!empty($request->another_mini_zone)) $selectedZones['mini'][] = $request->another_mini_zone;

        ContractedTreatment::create([
            'user_id' => $user->id,
            'branch_id' => $request->branch_id,
            'treatment_id' => $treatment->id,
            'contracted_packages' => [], // No hay paquetes financieros
            'contracted_additionals' => [],
            'selected_zones' => $selectedZones,
            'total_price' => 0, // No suma a contabilidad
            'status' => 'Paid', // Lo marcamos como pagado/activo
            'sessions' => $request->sessions,
            'days_between_sessions' => $treatment->days_between_sessions,
            'terms_acepted' => false, // Forzamos a que el paciente firme
            'is_pregnant' => false,
        ]);

        return redirect()->route('dashboard')->with('success', 'Tratamiento antiguo asignado correctamente. El paciente deberá firmar el consentimiento al iniciar sesión.');
    }
}

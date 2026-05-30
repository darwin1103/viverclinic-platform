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
        $rules = [
            'user_id' => 'required|exists:users,id',
            'treatment_id' => 'required|exists:treatments,id',
            'branch_id' => 'required|exists:branches,id',
            'sessions' => 'required|integer|min:1',
            'selected_zones' => 'nullable|array',
            'another_big_zone' => 'nullable|string',
            'another_mini_zone' => 'nullable|string',
            'payment_type' => 'required|in:full,installment,abono',
        ];

        if ($request->payment_type === 'abono') {
            $rules['total_price'] = 'required|numeric|min:0';
            $rules['legacy_paid_amount'] = 'required|numeric|min:0|max:' . ($request->total_price ?? 0);
        } elseif ($request->payment_type === 'installment') {
            $rules['installments'] = 'required|array';
            $rules['installments.*.price'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        // Aseguramos que el usuario es legacy
        $user = User::findOrFail($request->user_id);
        if (!$user->is_legacy) {
            return back()->with('error', 'El usuario seleccionado no está marcado como usuario antiguo.');
        }

        $treatment = Treatment::findOrFail($request->treatment_id);

        $selectedZones = $request->selected_zones ?? ['big' => [], 'mini' => []];
        if (!empty($request->another_big_zone)) $selectedZones['big'][] = $request->another_big_zone;
        if (!empty($request->another_mini_zone)) $selectedZones['mini'][] = $request->another_mini_zone;

        $paymentType = $request->payment_type;
        $totalPrice = 0.00;
        $legacyPaidAmount = 0.00;
        $status = 'Paid';

        if ($paymentType === 'abono') {
            $totalPrice = (float) $request->total_price;
            $legacyPaidAmount = (float) $request->legacy_paid_amount;
            $status = ($totalPrice - $legacyPaidAmount <= 0) ? 'Paid' : 'Pending';
        } elseif ($paymentType === 'installment') {
            foreach ($request->installments as $inst) {
                $totalPrice += (float) ($inst['price'] ?? 0);
            }
            $legacyPaidAmount = 0.00;
            $status = 'Pending';
        }

        $contractedTreatment = ContractedTreatment::create([
            'user_id' => $user->id,
            'branch_id' => $request->branch_id,
            'treatment_id' => $treatment->id,
            'contracted_packages' => [], // No hay paquetes financieros
            'contracted_additionals' => [],
            'selected_zones' => $selectedZones,
            'total_price' => $totalPrice,
            'legacy_paid_amount' => $legacyPaidAmount,
            'payment_type' => $paymentType,
            'status' => $status,
            'sessions' => $request->sessions,
            'days_between_sessions' => $treatment->days_between_sessions,
            'terms_acepted' => false, // Forzamos a que el paciente firme
            'is_pregnant' => false,
        ]);

        if ($paymentType === 'installment') {
            foreach ($request->installments as $num => $inst) {
                $contractedTreatment->installments()->create([
                    'installment_number' => (int) $num,
                    'price' => (float) ($inst['price'] ?? 0),
                    'status' => 'PENDING'
                ]);
            }
        }

        return redirect()->route('dashboard')->with('success', 'Tratamiento antiguo asignado correctamente. El paciente deberá firmar el consentimiento al iniciar sesión.');
    }
}

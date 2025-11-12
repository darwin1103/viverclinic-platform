<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreTreatmentRequest;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TreatmentController extends Controller
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
                'name' => 'Mini zona adicional',
                'price' => $treatment->price_additional_mini_zone,
            ],
            [
                'id' => 'big',
                'name' => 'Zona grande adicional',
                'price' => $treatment->price_additional_zone,
            ],
        ];

        $bigZones = Treatment::$bigZones;
        $smallZones = Treatment::$smallZones;
        $miniZones = Treatment::$miniZones;

        $treatment = $treatment;

        return view('client.treatment.show', compact(
            'packages',
            'additionalZones',
            'bigZones',
            'smallZones',
            'miniZones',
            'treatment'
        ));

    }

    public function store(StoreTreatmentRequest $request)
    {

        $validatedData = $request->validated();

        // --- 1. Get Context and Perform Critical Business Logic Validation ---

        $user = Auth::user();
        $branch = $user->patientProfile->branch; // Get branch from the authenticated user

        // Find the treatment from the submitted ID
        $treatment = Treatment::find($validatedData['treatment_id']);

        // A) Validate that the treatment exists
        if (!$treatment) {
            // Throw an exception that will redirect back with an error message
            throw ValidationException::withMessages([
                'treatment_id' => 'El tratamiento seleccionado no es válido.'
            ]);
        }

        // B) Validate that all submitted packages belong to this treatment and branch
        $submittedPackageIds = array_keys($validatedData['package'] ?? []);

        $validPackages = $treatment->packages()
            ->where('branch_id', $branch->id)
            ->whereIn('id', $submittedPackageIds)
            ->get()
            ->keyBy('id'); // keyBy('id') is great for easy lookups

        if (count($submittedPackageIds) !== $validPackages->count()) {
             throw ValidationException::withMessages([
                'package' => 'Uno o más de los paquetes seleccionados no son válidos para este tratamiento o sucursal.'
            ]);
        }


        // --- 2. Process and Prepare Data for Storage ---

        // A) Merge "another zone" fields into the selected zones array
        $selectedZones = $validatedData['selected_zones'] ?? ['big' => [], 'mini' => []];
        if (!empty($validatedData['another_big_zone'])) {
            $selectedZones['big'][] = $validatedData['another_big_zone'];
        }
        if (!empty($validatedData['another_mini_zone'])) {
            $selectedZones['mini'][] = $validatedData['another_mini_zone'];
        }

        // B) Calculate total price and build the data snapshots for JSON columns
        $totalPrice = 0;
        $contractedPackages = [];
        $contractedAdditionals = [];

        // Calculate from main packages
        foreach (($validatedData['package'] ?? []) as $id => $quantity) {
            $package = $validPackages->get($id);
            $priceAtPurchase = $package->price * $quantity;
            $totalPrice += $priceAtPurchase;

            $contractedPackages[] = [
                'id' => $package->id,
                'name' => $package->name,
                'quantity' => (int)$quantity,
                'price_at_purchase' => $package->price
            ];
        }

        // Calculate from additional zones (assuming prices are fixed or in a config)
        // For this example, let's use the prices from your original code.
        $additionalPrices = ['mini' => $treatment->price_additional_mini_zone, 'big' => $treatment->price_additional_zone];
        $additionalNames = ['mini' => 'Mini zona adicional', 'big' => 'Zona grande adicional'];

        foreach (($validatedData['additional'] ?? []) as $type => $quantity) {
            $priceAtPurchase = $additionalPrices[$type] * $quantity;
            $totalPrice += $priceAtPurchase;

             $contractedAdditionals[] = [
                'id' => $type, // 'mini' or 'big'
                'name' => $additionalNames[$type],
                'quantity' => (int)$quantity,
                'price_at_purchase' => $additionalPrices[$type]
            ];
        }

        // --- 3. Create the Database Record ---

        $contractedTreatment = ContractedTreatment::create([
            'user_id' => $user->id,
            'branch_id' => $branch->id,
            'treatment_id' => $treatment->id,
            'contracted_packages' => $contractedPackages,
            'contracted_additionals' => $contractedAdditionals,
            'selected_zones' => $selectedZones,
            'total_price' => $totalPrice,
            'status' => 'Pending', // Default status
            'sessions' => $treatment->sessions,
            'days_between_sessions' => $treatment->days_between_sessions,
            'terms_acepted' => ($validatedData['termsConditions'] == 1) ? true : false,
            'is_pregnant' => (isset($validatedData['notPregnant']) && $validatedData['notPregnant'] == 1) ? true : false,
        ]);


        // --- 4. Redirect with a Success Message ---

        // Redirigir a una página de confirmación o de pago
        return redirect()->route('client.contracted-treatment.index')->with('success', '¡Has seleccionado tu tratamiento! El siguiente paso es agendar tu cita.');
    }
}

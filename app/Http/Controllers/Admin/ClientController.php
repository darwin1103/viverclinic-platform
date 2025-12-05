<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\Role;
use App\Models\ToxicologicalCondition;
use App\Models\Treatment;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Traits\Filterable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientController extends Controller
{

    use Filterable;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        // Query base
        $query = User::select('id', 'name', 'email', 'created_at')
            ->role('PATIENT')
            ->with('patientProfile.branch'); // use select ***

        // Filter by branch using the relationship
        if ($request->filled('branch_id') || session('selected_branch_id')) {
            $query->whereHas('patientProfile', function ($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id') ?? session('selected_branch_id'));
            });
        }

        // Apply filters from the trait and paginate the results
        $clients = $this->applyFilters($request, $query)
                        ->latest() // Opcional: ordenar por los más recientes
                        ->paginate(10)
                        ->withQueryString(); // Importante para mantener los filtros en la paginación

        $branches = Branch::all();

        if ($request->filled('branch_id')) {
            session(['selected_branch_id' => $request->input('branch_id')]);
        }
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.client.index', compact('clients', 'branches', 'selectedBranchID'));
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('admin.client.create', compact('branches'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $messages = [
            // Reglas para 'name'
            'name.required'   => 'El campo nombre es obligatorio.',
            'name.string'     => 'El nombre debe ser una cadena de texto.',
            'name.max'        => 'El nombre no debe exceder los :max caracteres.',

            // Reglas para 'email'
            'email.required'  => 'El campo de correo electrónico es obligatorio.',
            'email.string'    => 'El correo electrónico debe ser una cadena de texto.',
            'email.email'     => 'El formato del correo electrónico no es válido.',
            'email.max'       => 'El correo electrónico no debe exceder los :max caracteres.',
            'email.unique'    => 'Este correo electrónico ya ha sido registrado.',

            // Reglas para 'branch_id'
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            // La regla 'integer' no está explícita, pero 'exists' y la naturaleza del 'id' la implican.
            'branch_id.exists'   => 'La sucursal seleccionada no existe en la base de datos.',
        ];

        $attributes = [
            'name'      => 'Nombre',
            'email'     => 'Correo Electrónico',
            'branch_id' => 'Sucursal',
        ];

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'branch_id' => 'required|exists:branches,id',
        ], $messages, $attributes);

        // Create new user with password
        $password = Str::random(12);

        $client = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
        ]);

        $client->assignRole('PATIENT');

        $client->patientProfile()->create([
            'branch_id' => $request->branchId,
        ]);

        $client->notify(new UserCreatedNotification($client->name, $client->email, $password));

        return redirect()->back()->with('success', 'User created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $client)
    {

        $client->load('patientProfile.branch'); // use select ***
        $branches = Branch::all();
        $genres = Gender::where('status', true)->get();
        $documentTypes = DocumentType::where('status', true)->get();
        $pathologicalConditions = PathologicalCondition::where('status', true)->get();
        $toxicologicalConditions = ToxicologicalCondition::where('status', true)->get();
        $gynecoObstetricConditions = GynecoObstetricCondition::where('status', true)->get();
        $medicationConditions = MedicationCondition::where('status', true)->get();
        $dietaryConditions = DietaryCondition::where('status', true)->get();
        $treatments = Treatment::where('active', true)->get();

        $data = [
            'client' => $client,
            'branches' => $branches,
            'genres' => $genres,
            'documentTypes' => $documentTypes,
            'pathologicalConditions' => $pathologicalConditions,
            'toxicologicalConditions' => $toxicologicalConditions,
            'gynecoObstetricConditions' => $gynecoObstetricConditions,
            'medicationConditions' => $medicationConditions,
            'dietaryConditions' => $dietaryConditions,
            'treatments' => $treatments,
        ];

        return view('admin.client.show', $data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $client)
    {
        $client->load('patientProfile.branch'); // use select ***

        $branches = Branch::all();

        return view('admin.client.edit', compact('client', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $client)
    {

        $messages = [
            // Reglas para 'name'
            'name.required'   => 'El campo nombre es obligatorio.',
            'name.string'     => 'El nombre debe ser una cadena de texto.',
            'name.max'        => 'El nombre no debe exceder los :max caracteres.',

            // Reglas para 'email'
            'email.required'  => 'El campo de correo electrónico es obligatorio.',
            'email.string'    => 'El correo electrónico debe ser una cadena de texto.',
            'email.email'     => 'El formato del correo electrónico no es válido.',
            'email.max'       => 'El correo electrónico no debe exceder los :max caracteres.',
            'email.unique'    => 'Este correo electrónico ya ha sido registrado.',

            // Reglas para 'branch_id'
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            // La regla 'integer' no está explícita, pero 'exists' y la naturaleza del 'id' la implican.
            'branch_id.exists'   => 'La sucursal seleccionada no existe en la base de datos.',
        ];

        $attributes = [
            'name'      => 'Nombre',
            'email'     => 'Correo Electrónico',
            'branch_id' => 'Sucursal',
        ];

        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|string|email|max:255|unique:users',
            'branch_id' => 'required|exists:branches,id',
        ], $messages, $attributes);

        $client->name = $request->name;
        $client->email = $request->email;
        $client->save();

        $client->patientProfile()->update([
            'branch_id' => $request->branchId,
        ]);

        return redirect()->back()->with('success', 'Successful operation');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $client)
    {

        $client->delete();

        return redirect()->back()->with('success', 'Successful operation');

    }

    public function getusers(string $roleId) {

        $clients = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', self::SUPER_ADMIN_ROLE_ID);
        })
        ->with('roles')
        ->get();

        $role = Role::where('id', $roleId)->first();

        $response = [];

        foreach ($clients as $key => $client) {

            $response[] = [
                'id' => $client->id,
                'name' => $client->name,
                'contains' => ($client->roles->contains($role)) ? 'checked' : '',
            ];
        }

        return response()->json([
            'users' => $response
        ]);

    }
    
}

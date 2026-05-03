<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminProfile;
use App\Models\Branch;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminManagerController extends Controller
{

    use Filterable;

    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of admin users.
     */
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'created_at')
            ->role('ADMIN')
            ->with('adminProfile.branch');

        // Filter by branch
        if ($request->filled('branch_id') || session('selected_branch_id')) {
            $query->whereHas('adminProfile', function ($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id') ?? session('selected_branch_id'));
            });
        }

        $admins = $this->applyFilters($request, $query)
                        ->latest()
                        ->paginate(10)
                        ->withQueryString();

        $branches = Branch::all();

        if ($request->has('branch_id')) {
            if ($request->filled('branch_id')) {
                session(['selected_branch_id' => $request->input('branch_id')]);
            } else {
                session()->forget('selected_branch_id');
            }
        }
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.admin-manager.index', compact('admins', 'branches', 'selectedBranchID'));
    }

    /**
     * Show the form for creating a new admin.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('admin.admin-manager.create', compact('branches'));
    }

    /**
     * Store a newly created admin.
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required'      => 'El campo nombre es obligatorio.',
            'name.max'           => 'El nombre no debe exceder los :max caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo electrónico no es válido.',
            'email.unique'       => 'Este correo electrónico ya ha sido registrado.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists'   => 'La sucursal seleccionada no existe.',
            'salary.required'    => 'El sueldo es obligatorio.',
            'salary.numeric'     => 'El sueldo debe ser un valor numérico.',
            'salary.min'         => 'El sueldo no puede ser negativo.',
        ];

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|string|email|max:255|unique:users',
            'branch_id'          => 'required|exists:branches,id',
            'salary'             => 'required|numeric|min:0',
            'commission_divisor' => 'nullable|integer|min:1',
            'commission_base'    => 'nullable|numeric|min:0',
        ], $messages);

        DB::transaction(function () use ($validated) {

            $password = Str::random(12);

            $admin = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($password),
            ]);

            $admin->assignRole('ADMIN');

            $admin->notify(new UserCreatedNotification($admin->name, $admin->email, $password));

            $admin->adminProfile()->create([
                'branch_id'          => $validated['branch_id'],
                'salary'             => $validated['salary'],
                'commission_divisor' => $validated['commission_divisor'] ?? 30,
                'commission_base'    => $validated['commission_base'] ?? 2500000,
            ]);

            // Assign to admins_branches pivot
            $admin->adminsBranches()->attach($validated['branch_id']);
        });

        return redirect()->route('admin.admin-manager.index')->with('success', 'Administrador creado exitosamente.');
    }

    /**
     * Show the form for editing the specified admin.
     */
    public function edit(User $admin_manager)
    {
        $branches = Branch::all();
        $admin_manager->load('adminProfile');
        return view('admin.admin-manager.edit', compact('admin_manager', 'branches'));
    }

    /**
     * Update the specified admin.
     */
    public function update(Request $request, User $admin_manager)
    {
        $messages = [
            'name.required'      => 'El campo nombre es obligatorio.',
            'name.max'           => 'El nombre no debe exceder los :max caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo electrónico no es válido.',
            'email.unique'       => 'Este correo electrónico ya ha sido registrado.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists'   => 'La sucursal seleccionada no existe.',
            'salary.required'    => 'El sueldo es obligatorio.',
        ];

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|string|email|max:255|unique:users,email,' . $admin_manager->id,
            'branch_id'          => 'required|exists:branches,id',
            'salary'             => 'required|numeric|min:0',
            'commission_divisor' => 'nullable|integer|min:1',
            'commission_base'    => 'nullable|numeric|min:0',
        ], $messages);

        DB::transaction(function () use ($validated, $admin_manager) {
            $admin_manager->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]);

            $admin_manager->adminProfile()->updateOrCreate(
                ['user_id' => $admin_manager->id],
                [
                    'branch_id'          => $validated['branch_id'],
                    'salary'             => $validated['salary'],
                    'commission_divisor' => $validated['commission_divisor'] ?? 30,
                    'commission_base'    => $validated['commission_base'] ?? 2500000,
                ]
            );

            // Sync admins_branches pivot
            $admin_manager->adminsBranches()->sync([$validated['branch_id']]);
        });

        return redirect()->back()->with('success', 'Administrador actualizado exitosamente.');
    }

    /**
     * Remove the specified admin.
     */
    public function destroy(User $admin_manager)
    {
        $admin_manager->delete();
        return redirect()->back()->with('success', 'Administrador eliminado exitosamente.');
    }
}

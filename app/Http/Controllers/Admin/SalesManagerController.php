<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SalesProfile;
use App\Models\Branch;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use App\Traits\Filterable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SalesManagerController extends Controller
{
    use Filterable;

    public function __construct()
    {
        $this->middleware('auth');
        // Este controlador debe estar protegido por un middleware o grupo de rutas para SUPER_ADMIN|OWNER
    }

    /**
     * Display a listing of sales users.
     */
    public function index(Request $request)
    {
        $query = User::select('id', 'name', 'email', 'created_at')
            ->role('SALES')
            ->with('salesProfile.branch');

        // Filter by branch
        if ($request->filled('branch_id') || session('selected_branch_id')) {
            $query->whereHas('salesProfile', function ($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id') ?? session('selected_branch_id'));
            });
        }

        $salesUsers = $this->applyFilters($request, $query)
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

        return view('admin.sales-manager.index', compact('salesUsers', 'branches', 'selectedBranchID'));
    }

    /**
     * Show the form for creating a new sales user.
     */
    public function create()
    {
        $branches = Branch::all();
        return view('admin.sales-manager.create', compact('branches'));
    }

    /**
     * Store a newly created sales user.
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
            'commission_divisor.integer' => 'El divisor de comisión debe ser un número entero.',
            'commission_divisor.min' => 'El divisor de comisión no puede ser menor a 1.',
        ];

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|string|email|max:255|unique:users',
            'branch_id'          => 'required|exists:branches,id',
            'commission_divisor' => 'nullable|integer|min:1',
        ], $messages);

        DB::transaction(function () use ($validated) {

            $password = Str::random(12);

            $salesUser = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($password),
            ]);

            $salesUser->assignRole('SALES');

            $salesUser->notify(new UserCreatedNotification($salesUser->name, $salesUser->email, $password));

            $salesUser->salesProfile()->create([
                'branch_id'          => $validated['branch_id'],
                'commission_divisor' => $validated['commission_divisor'] ?? 26,
            ]);
        });

        return redirect()->route('admin.sales-manager.index')->with('success', 'Vendedor creado exitosamente.');
    }

    /**
     * Show the form for editing the specified sales user.
     */
    public function edit(User $sales_manager)
    {
        $branches = Branch::all();
        $sales_manager->load('salesProfile');
        return view('admin.sales-manager.edit', compact('sales_manager', 'branches'));
    }

    /**
     * Update the specified sales user.
     */
    public function update(Request $request, User $sales_manager)
    {
        $messages = [
            'name.required'      => 'El campo nombre es obligatorio.',
            'name.max'           => 'El nombre no debe exceder los :max caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'El formato del correo electrónico no es válido.',
            'email.unique'       => 'Este correo electrónico ya ha sido registrado.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists'   => 'La sucursal seleccionada no existe.',
        ];

        $validated = $request->validate([
            'name'               => 'required|string|max:255',
            'email'              => 'required|string|email|max:255|unique:users,email,' . $sales_manager->id,
            'branch_id'          => 'required|exists:branches,id',
            'commission_divisor' => 'nullable|integer|min:1',
        ], $messages);

        DB::transaction(function () use ($validated, $sales_manager) {
            $sales_manager->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
            ]);

            $sales_manager->salesProfile()->updateOrCreate(
                ['user_id' => $sales_manager->id],
                [
                    'branch_id'          => $validated['branch_id'],
                    'commission_divisor' => $validated['commission_divisor'] ?? 26,
                ]
            );
        });

        return redirect()->back()->with('success', 'Vendedor actualizado exitosamente.');
    }

    /**
     * Remove the specified sales user.
     */
    public function destroy(User $sales_manager)
    {
        $sales_manager->delete();
        return redirect()->back()->with('success', 'Vendedor eliminado exitosamente.');
    }
}

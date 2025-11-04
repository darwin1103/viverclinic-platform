<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\OwnerProfile;
use App\Models\User;
use App\Models\WorkSchedule;
use App\Notifications\UserCreatedNotification;
use App\Traits\Filterable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
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
            ->role('OWNER')
            ->with('ownerProfile.branch'); // use select ***

        // Filter by branch using the relationship
        if ($request->filled('branch_id')) {
            $query->whereHas('ownerProfile', function ($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id'));
            });
        }

        // Apply filters from the trait and paginate the results
        $owners = $this->applyFilters($request, $query)
                        ->latest() // Opcional: ordenar por los más recientes
                        ->paginate(10)
                        ->withQueryString(); // Importante para mantener los filtros en la paginación

        $branches = Branch::all();

        $selectedBranchID = $request->input('branch_id') ?? '';

        return view('admin.owner.index', compact('owners', 'branches', 'selectedBranchID'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();

        $data = [
            'branches' => $branches,
        ];

        return view('admin.owner.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'branch_id' => 'required|exists:branches,id',
        ]);

        DB::transaction(function () use ($validated, $request) {

            $password = Str::random(12);

            $owner = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
            ]);

            $owner->assignRole('OWNER');

            // custom notification for the EMPLOYEE ***

            $owner->notify(new UserCreatedNotification($owner->name, $owner->email, $password));

            $ownerProfile = $owner->ownerProfile()->create([
                'branch_id' => $validated['branch_id']
            ]);

        });

        return redirect()->route('owner.index')->with('success', 'User created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $owner)
    {

        $branches = Branch::all();

        $data = [
            'owner' => $owner,
            'branches' => $branches,
            'daysOfWeek' => WorkSchedule::$daysOfWeek,
        ];

        return view('admin.owner.show', $data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $owner)
    {

        $branches = Branch::all();

        return view('admin.owner.edit', compact('owner', 'branches'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $owner)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($owner->id)],
            'branch_id' => 'required|exists:branches,id',
        ]);

        DB::transaction(function () use ($validated, $request, $owner) {
            $ownerData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            $owner->update($ownerData);

            $owner->ownerProfile()->update([
                'branch_id' => $validated['branch_id']
            ]);

        });

        return redirect()->back()->with('success', 'Successful operation');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $owner)
    {

        $owner->delete();

        return redirect()->back()->with('success', 'Successful operation');

    }

}

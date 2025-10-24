<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\StaffProfile;
use App\Models\WorkSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class StaffController extends Controller
{

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
    public function index()
    {

        $staffs = User::select('id','name','created_at','updated_at')
            ->role('EMPLOYEE')
            ->paginate(10);

        return view('staff.index', compact('staffs'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $branches = Branch::all();

        $data = [
            'branches' => $branches,
            'daysOfWeek' => WorkSchedule::$daysOfWeek,
        ];

        return view('staff.create', $data);
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
            'schedules' => 'nullable|array',
            'schedules.*' => 'required|array',
            'schedules.*.*.start_time' => 'required|date_format:H:i',
            'schedules.*.*.end_time' => 'required|date_format:H:i|after:schedules.*.*.start_time',
        ]);

        DB::transaction(function () use ($validated, $request) {

            $staff = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make(Str::random(12)),
            ]);

            $staff->assignRole('EMPLOYEE');

            // custom notification for the EMPLOYEE ***

            // $staff->notify(new UserCreatedNotification($staff->name, $staff->email, $password));

            $staffProfile = $staff->staffProfile()->create([
                'branch_id' => $validated['branch_id']
            ]);

            if (isset($validated['schedules'])) {
                foreach ($validated['schedules'] as $day => $times) {
                    foreach ($times as $time) {
                        $staffProfile->workSchedules()->create([
                            'day_of_week' => $day,
                            'start_time' => $time['start_time'],
                            'end_time' => $time['end_time'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('staff.index')->with('success', 'User created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $staff)
    {

        $schedules = $staff->staffProfile->workSchedules->groupBy('day_of_week');

        $branches = Branch::all();

        $data = [
            'staff' => $staff,
            'schedules' => $schedules,
            'branches' => $branches,
            'daysOfWeek' => WorkSchedule::$daysOfWeek,
        ];

        return view('staff.show', $data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {

        $branches = Branch::all();
        $daysOfWeek = WorkSchedule::$daysOfWeek;
        $schedules = $staff->staffProfile->workSchedules->groupBy('day_of_week');

        return view('staff.edit', compact('staff', 'branches', 'daysOfWeek', 'schedules'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($staff->id)],
            'branch_id' => 'required|exists:branches,id',
            'schedules' => 'nullable|array',
            'schedules.*' => 'required|array',
            'schedules.*.*.start_time' => 'required|date_format:H:i',
            'schedules.*.*.end_time' => 'required|date_format:H:i|after:schedules.*.*.start_time',
        ]);

        DB::transaction(function () use ($validated, $request, $staff) {
            $staffData = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];

            $staff->update($staffData);

            $staff->staffProfile()->update([
                'branch_id' => $validated['branch_id']
            ]);

            // Delete old schedules
            $staff->staffProfile->workSchedules()->delete();

            // Create new schedules
            if (isset($validated['schedules'])) {
                foreach ($validated['schedules'] as $day => $times) {
                    foreach ($times as $time) {
                        $staff->staffProfile->workSchedules()->create([
                            'day_of_week' => $day,
                            'start_time' => $time['start_time'],
                            'end_time' => $time['end_time'],
                        ]);
                    }
                }
            }
        });

        return redirect()->back()->with('success', 'Successful operation');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $staff)
    {

        $staff->delete();

        return redirect()->back()->with('success', 'Successful operation');

    }

}

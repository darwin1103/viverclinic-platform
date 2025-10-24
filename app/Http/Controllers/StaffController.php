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
use Carbon\Carbon;

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
                        $adjustedStartTime = $this->adjustTimeToNearestQuarterHour($time['start_time']);
                        $adjustedEndTime = $this->adjustTimeToNearestQuarterHour($time['end_time']);
                        $staffProfile->workSchedules()->create([
                            'day_of_week' => $day,
                            'start_time' => $adjustedStartTime,
                            'end_time' => $adjustedEndTime,
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


        $appointments = [
            [
                'date' => '05-10-2025',
                'client_name' => 'Maria Paredes',
                'treatment' => 'Depilación láser',
                'branch' => 'Sede Principal'
            ],
            [
                'date' => '08-10-2025',
                'client_name' => 'Juan Rodriguez',
                'treatment' => 'Reducción',
                'branch' => 'Sucursal Norte'
            ],
            [
                'date' => '12-10-2025',
                'client_name' => 'Ana Garcia',
                'treatment' => 'Depilación láser',
                'branch' => 'Sede Principal'
            ],
            [
                'date' => '15-10-2025',
                'client_name' => 'Carlos Sanchez',
                'treatment' => 'Reducción',
                'branch' => 'Sucursal Sur'
            ],
        ];

        $data = [
            'staff' => $staff,
            'schedules' => $schedules,
            'branches' => $branches,
            'appointments' => $appointments,
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
                        $adjustedStartTime = $this->adjustTimeToNearestQuarterHour($time['start_time']);
                        $adjustedEndTime = $this->adjustTimeToNearestQuarterHour($time['end_time']);
                        $staff->staffProfile->workSchedules()->create([
                            'day_of_week' => $day,
                            'start_time' => $adjustedStartTime,
                            'end_time' => $adjustedEndTime,
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

    /**
     * Adjusts a time string to the nearest 15-minute interval.
     *
     * @param string $timeString The time in H:i format (e.g., "16:19").
     * @return string The adjusted time in H:i format (e.g., "16:15").
     */
    private function adjustTimeToNearestQuarterHour(string $timeString): string
    {
        // 1. Create a Carbon instance from the time string.
        $time = Carbon::createFromFormat('H:i', $timeString);

        // 2. Calculate the rounded minute.
        // round(19 / 15) * 15 = round(1.26) * 15 = 1 * 15 = 15
        // round(55 / 15) * 15 = round(3.66) * 15 = 4 * 15 = 60
        // round(7 / 15)  * 15 = round(0.46) * 15 = 0 * 15 = 0
        $roundedMinute = round($time->minute / 15) * 15;

        // 3. Handle the hour rollover case.
        if ($roundedMinute >= 60) {
            // If minutes round up to 60, add an hour and reset minutes to 0.
            $time->addHour();
            $time->minute(0);
        } else {
            // Otherwise, just set the calculated minute.
            $time->minute($roundedMinute);
        }

        // 4. Ensure seconds are always 0 for consistency.
        $time->second(0);

        // 5. Return the formatted time string.
        return $time->format('H:i');
    }

}

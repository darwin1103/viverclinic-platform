<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Branch;
use App\Models\StaffProfile;
use App\Models\Treatment;
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

class StaffController extends Controller
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
            ->role('EMPLOYEE')
            ->with('staffProfile.branch'); // use select ***

        // Filter by branch using the relationship
        if ($request->filled('branch_id')) {
            $query->whereHas('staffProfile', function ($q) use ($request) {
                $q->where('branch_id', $request->input('branch_id'));
            });
        }

        // Apply filters from the trait and paginate the results
        $staffs = $this->applyFilters($request, $query)
                        ->latest() // Opcional: ordenar por los más recientes
                        ->paginate(10)
                        ->withQueryString(); // Importante para mantener los filtros en la paginación

        $branches = Branch::all();

        $selectedBranchID = $request->input('branch_id') ?? '';

        return view('admin.staff.index', compact('staffs', 'branches', 'selectedBranchID'));

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

        return view('admin.staff.create', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $messages = [
            // Reglas de nivel superior (name, email, branch_id)
            'name.required'   => 'El campo nombre es obligatorio.',
            'name.max'        => 'El nombre no debe exceder los :max caracteres.',
            'email.required'  => 'El correo electrónico es obligatorio.',
            'email.email'     => 'El formato del correo electrónico no es válido.',
            'email.unique'    => 'Este correo electrónico ya ha sido registrado.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists'   => 'La sucursal seleccionada no existe.',

            // Reglas para el array 'schedules'
            'schedules.array'      => 'El campo de horarios debe ser un arreglo.',
            'schedules.*.required' => 'Cada día dentro del arreglo de horarios debe ser especificado.',
            'schedules.*.array'    => 'Cada día en los horarios debe ser un arreglo de horas.',

            // Reglas anidadas para las horas ('schedules.*.*')
            'schedules.*.*.start_time.required'  => 'La hora de inicio es obligatoria para cada bloque.',
            'schedules.*.*.start_time.date_format' => 'La hora de inicio debe tener el formato HH:MM (ej: 14:30).',

            'schedules.*.*.end_time.required'    => 'La hora de fin es obligatoria para cada bloque.',
            'schedules.*.*.end_time.date_format' => 'La hora de fin debe tener el formato HH:MM (ej: 18:00).',
            'schedules.*.*.end_time.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];

        $attributes = [
            'name'      => 'Nombre',
            'email'     => 'Correo Electrónico',
            'branch_id' => 'Sucursal',
            'schedules' => 'Horarios',
            'schedules.*' => 'Día de la Semana', // Para mejorar el mensaje cuando falla schedules.*.required/array
            'schedules.*.*.start_time' => 'Hora de Inicio', // Útil si se usa el archivo de idioma
            'schedules.*.*.end_time'   => 'Hora de Fin',    // Útil si se usa el archivo de idioma
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'branch_id' => 'required|exists:branches,id',
            'schedules' => 'nullable|array',
            'schedules.*' => 'required|array',
            'schedules.*.*.start_time' => 'required|date_format:H:i',
            'schedules.*.*.end_time' => 'required|date_format:H:i|after:schedules.*.*.start_time',
        ], $messages, $attributes);

        DB::transaction(function () use ($validated, $request) {

            $password = Str::random(12);

            $staff = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($password),
            ]);

            $staff->assignRole('EMPLOYEE');

            // custom notification for the EMPLOYEE ***

            $staff->notify(new UserCreatedNotification($staff->name, $staff->email, $password));

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

        return redirect()->route('admin.staff.index')->with('success', 'User created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $staff, Request $request)
    {

        $schedules = $staff->staffProfile->workSchedules->groupBy('day_of_week');

        $branches = Branch::all();

        // Start query for appointments assigned to the current staff member
        $query = Appointment::where('staff_user_id', $staff->id)
            ->with(['contractedTreatment.user', 'contractedTreatment.treatment']);

        // Apply search filter for patient name
        if ($request->filled('search')) {
            $query->whereHas('contractedTreatment.user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });
        }

        // Apply filter for treatment
        if ($request->filled('treatment_id')) {
            $query->whereHas('contractedTreatment', function ($q) use ($request) {
                $q->where('treatment_id', $request->treatment_id);
            });
        }

        // Apply date filters
        if ($request->filled('min_date')) {
            $query->whereDate('schedule', '>=', $request->min_date);
        }
        if ($request->filled('max_date')) {
            $query->whereDate('schedule', '<=', $request->max_date);
        }

        // Order by most recent schedule and paginate
        $appointments = $query->orderBy('schedule', 'desc')->paginate(15);

        $treatments = Treatment::orderBy('name')->get();

        $isOnAppointmentTable = ($request->filled('is_on_appointment_table') || $request->has('page')) ? true : false;

        $data = [
            'staff' => $staff,
            'schedules' => $schedules,
            'branches' => $branches,
            'appointments' => $appointments,
            'daysOfWeek' => WorkSchedule::$daysOfWeek,
            'treatments' => $treatments,
            'isOnAppointmentTable' => $isOnAppointmentTable,
        ];

        return view('admin.staff.show', $data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $staff)
    {

        $branches = Branch::all();
        $daysOfWeek = WorkSchedule::$daysOfWeek;
        $schedules = $staff->staffProfile->workSchedules->groupBy('day_of_week');

        return view('admin.staff.edit', compact('staff', 'branches', 'daysOfWeek', 'schedules'));

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $staff)
    {

        $messages = [
            // Reglas de nivel superior (name, email, branch_id)
            'name.required'   => 'El campo nombre es obligatorio.',
            'name.max'        => 'El nombre no debe exceder los :max caracteres.',
            'email.required'  => 'El correo electrónico es obligatorio.',
            'email.email'     => 'El formato del correo electrónico no es válido.',
            'email.unique'    => 'Este correo electrónico ya ha sido registrado.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists'   => 'La sucursal seleccionada no existe.',

            // Reglas para el array 'schedules'
            'schedules.array'      => 'El campo de horarios debe ser un arreglo.',
            'schedules.*.required' => 'Cada día dentro del arreglo de horarios debe ser especificado.',
            'schedules.*.array'    => 'Cada día en los horarios debe ser un arreglo de horas.',

            // Reglas anidadas para las horas ('schedules.*.*')
            'schedules.*.*.start_time.required'  => 'La hora de inicio es obligatoria para cada bloque.',
            'schedules.*.*.start_time.date_format' => 'La hora de inicio debe tener el formato HH:MM (ej: 14:30).',

            'schedules.*.*.end_time.required'    => 'La hora de fin es obligatoria para cada bloque.',
            'schedules.*.*.end_time.date_format' => 'La hora de fin debe tener el formato HH:MM (ej: 18:00).',
            'schedules.*.*.end_time.after'       => 'La hora de fin debe ser posterior a la hora de inicio.',
        ];

        $attributes = [
            'name'      => 'Nombre',
            'email'     => 'Correo Electrónico',
            'branch_id' => 'Sucursal',
            'schedules' => 'Horarios',
            'schedules.*' => 'Día de la Semana', // Para mejorar el mensaje cuando falla schedules.*.required/array
            'schedules.*.*.start_time' => 'Hora de Inicio', // Útil si se usa el archivo de idioma
            'schedules.*.*.end_time'   => 'Hora de Fin',    // Útil si se usa el archivo de idioma
        ];

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'branch_id' => 'required|exists:branches,id',
            'schedules' => 'nullable|array',
            'schedules.*' => 'required|array',
            'schedules.*.*.start_time' => 'required|date_format:H:i',
            'schedules.*.*.end_time' => 'required|date_format:H:i|after:schedules.*.*.start_time',
        ], $messages, $attributes);

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
        $roundedMinute = round($time->minute / 20) * 20;

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

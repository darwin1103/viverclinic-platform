<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Branch;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {

        $user = Auth::user();
        if ($user->hasRole(['SUPER_ADMIN', 'OWNER', 'ADMIN']) || $user->hasPermissionTo('admin_dashboard')) {

            // 1. Total de pacientes (filtrado por sucursal automáticamente si aplica)
            // Se asume que User::role('PATIENT') respeta el global scope en otras vistas, pero si no
            // se aplicará al conteo base.
            $totalPatients = User::role('PATIENT')->count();

            // 2. Citas del mes actual
            $appointmentsThisMonth = Appointment::whereMonth('schedule', now()->month)
                                        ->whereYear('schedule', now()->year)
                                        ->count();

            // 3. Tratamientos activos (Asumiendo que 'Pending' es el estado activo o simplemente count de todo)
            $activeTreatments = ContractedTreatment::where('status', 'Pending')->count();

            // KPIs originales mantenidos si son necesarios para la vista
            $todayAppointments = Appointment::whereBetween('schedule', [
                today()->startOfDay(),
                today()->endOfDay(),
            ])->count();

            $todayAppointmentsList = Appointment::whereBetween('schedule', [
                today()->startOfDay(),
                today()->endOfDay(),
            ])
            ->with([
                'contractedTreatment.user',
                'contractedTreatment.treatment'
            ])
            ->get();

            $patientListQuery = User::role('PATIENT')
                ->when(request('date_from'), function ($q) {
                    $q->whereDate('created_at', '>=', request('date_from'));
                }, function ($q) {
                    $q->whereDate('created_at', '>=', now()->subDays(7));
                })
                ->when(request('date_to'), function ($q) {
                    $q->whereDate('created_at', '<=', request('date_to'));
                })
                ->when(request('branch_id') ?: session('selected_branch_id'), function ($q, $branchId) {
                    $q->whereHas('patientsBranches', function ($q2) use ($branchId) {
                        $q2->where('branches.id', $branchId);
                    });
                });

            $patientCount = $patientListQuery->count();

            $patientList = (clone $patientListQuery)
                ->select(['id', 'name'])
                ->latest()
                ->take(10)
                ->get();

            $branches = Branch::select(['id', 'name'])->get();
            $rolesExcluyendoPaciente = \Spatie\Permission\Models\Role::where('name', '!=', 'PATIENT')->pluck('name')->toArray();
            $professionals = User::role($rolesExcluyendoPaciente)->select(['id', 'name'])->get();

            // Preparar datos para Chart.js (Citas de los últimos 7 días)
            $appointmentsLast7Days = Appointment::selectRaw('DATE(schedule) as date, count(*) as count')
                ->whereDate('schedule', '>=', now()->subDays(6)->startOfDay())
                ->whereDate('schedule', '<=', now()->endOfDay())
                ->groupBy('date')
                ->orderBy('date', 'ASC')
                ->get();

            $chartLabels = [];
            $chartValues = [];

            for ($i = 6; $i >= 0; $i--) {
                $dateString = now()->subDays($i)->format('Y-m-d');
                $chartLabels[] = now()->subDays($i)->isoFormat('ddd D'); // e.g. "lun 22"
                $record = $appointmentsLast7Days->firstWhere('date', $dateString);
                $chartValues[] = $record ? $record->count : 0;
            }

            $recentPayments = \App\Models\TreatmentOrder::with(['user', 'contractedTreatment.treatment'])
                ->when(request('date_from'), function ($q) {
                    $q->whereDate('created_at', '>=', request('date_from'));
                })
                ->when(request('date_to'), function ($q) {
                    $q->whereDate('created_at', '<=', request('date_to'));
                })
                ->when(request('branch_id') ?: session('selected_branch_id'), function ($q, $branchId) {
                    $q->whereHas('contractedTreatment', function ($q2) use ($branchId) {
                        $q2->where('branch_id', $branchId);
                    });
                })
                ->latest()
                ->take(10)
                ->get();

            // Cálculos Reales
            $ingresoDiario = \App\Models\TreatmentOrder::whereDate('created_at', today())
                ->whereIn('status', ['Pagado', 'Paid', 'Pago completado', 'Aprobado'])
                ->sum('total');

            $egresosHoy = \App\Models\AccountingRecord::whereDate('created_at', today())
                ->where('type', 'expense')
                ->sum('amount');

            $ingresosPorCategoria = \App\Models\TreatmentOrder::whereDate('treatment_orders.created_at', today())
                ->whereIn('treatment_orders.status', ['Pagado', 'Paid', 'Pago completado', 'Aprobado'])
                ->join('contracted_treatments', 'treatment_orders.contracted_treatment_id', '=', 'contracted_treatments.id')
                ->join('treatments', 'contracted_treatments.treatment_id', '=', 'treatments.id')
                ->selectRaw('treatments.name as category, SUM(treatment_orders.total) as total')
                ->groupBy('treatments.name')
                ->get();

            $pagosPendientesCount = \App\Models\TreatmentOrder::whereIn('status', ['Pendiente', 'Pending', 'Pago por verificar'])->count();
            $reagendarCount = \App\Models\Appointment::whereIn('status', ['No asistió', 'Cancelada', 'Cancelado'])->count();

            $actividadReciente = \App\Models\TreatmentOrder::with(['user', 'contractedTreatment.treatment'])
                ->latest()
                ->take(3)
                ->get();

            $data = [
                'todayAppointments' => $todayAppointments,
                'todayAppointmentsList' => $todayAppointmentsList,
                'patientCount' => $patientCount,
                'patientList' => $patientList,
                'branches' => $branches,
                'selectedBranchID' => session('selected_branch_id', ''),
                'professionals' => $professionals,
                'recentPayments' => $recentPayments,
                // Nuevas métricas
                'totalPatients' => $totalPatients,
                'appointmentsThisMonth' => $appointmentsThisMonth,
                'activeTreatments' => $activeTreatments,
                'chartLabels' => $chartLabels,
                'chartValues' => $chartValues,
                // Nuevos cálculos dinámicos
                'ingresoDiario' => $ingresoDiario,
                'egresosHoy' => $egresosHoy,
                'ingresosPorCategoria' => $ingresosPorCategoria,
                'pagosPendientesCount' => $pagosPendientesCount,
                'reagendarCount' => $reagendarCount,
                'actividadReciente' => $actividadReciente,
            ];

            return view('dashboards.admin', $data);

        } elseif ($user->hasRole('EMPLOYEE')) {

            return view('dashboards.employee');

        } elseif ($user->hasRole('PATIENT')) {

            if (!Auth::user()->informed_consent) {
                return redirect()->route('client.informed-consent.create');
            }

            $user = Auth::user();

            $contractedTreatments = ContractedTreatment::where('user_id', $user->id)
                ->select(['id'])
                ->get();

            if ($contractedTreatments->count() > 1) {
                $createAppointmentUrl = route('client.contracted-treatment.index');
            } elseif ($contractedTreatments->count() == 1) {
                $createAppointmentUrl = route('client.schedule-appointment.index', ['contracted_treatment' =>  $contractedTreatments[0]->id]);
            } else {
                $createAppointmentUrl = null;
            }

            // --- Cálculos dinámicos para el Frontend ---

            // 1. Próxima Cita
            $nextAppointment = Appointment::whereHas('contractedTreatment', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where('schedule', '>=', now())
            ->whereIn('status', ['Agendado', 'Confirmado', 'Pendiente', 'Pending', 'Scheduled', 'Confirmed'])
            ->orderBy('schedule', 'asc')
            ->first();

            // 2. Saldo (Valor base hasta que se migre VirtualWallet real)
            $walletBalance = $user->virtualWallet->balance ?? 0.00;

            // 3. Paquetes Activos
            $activePackagesCount = ContractedTreatment::where('user_id', $user->id)
                ->whereIn('status', ['Pending', 'Activo', 'In Progress'])
                ->count();

            // 4. Últimas Recomendaciones
            $latestRecommendations = \App\Models\Recommendation::latest()->take(3)->get();

            // 5. Progreso del Tratamiento (del paquete más reciente)
            $latestActiveTreatment = ContractedTreatment::with('appointments', 'treatment')
                ->where('user_id', $user->id)
                ->whereIn('status', ['Pending', 'Activo', 'In Progress'])
                ->latest()
                ->first();

            $treatmentProgress = 0;
            $treatmentName = 'Ningún tratamiento activo';

            if ($latestActiveTreatment && $latestActiveTreatment->sessions > 0) {
                $completedSessions = $latestActiveTreatment->appointments()
                    ->where(function ($query) {
                        $query->whereIn('status', ['Completada', 'Completado', 'Completed'])
                              ->orWhere('attended', true);
                    })
                    ->count();
                
                $treatmentProgress = min(100, (int) round(($completedSessions / $latestActiveTreatment->sessions) * 100));
                $treatmentName = $latestActiveTreatment->treatment->name ?? 'Tratamiento actual';
            }

            $data = [
                'createAppointmentUrl' => $createAppointmentUrl,
                'nextAppointment'      => $nextAppointment,
                'walletBalance'        => $walletBalance,
                'activePackagesCount'  => $activePackagesCount,
                'latestRecommendations'=> $latestRecommendations,
                'treatmentProgress'    => $treatmentProgress,
                'treatmentName'        => $treatmentName,
            ];

            return view('dashboards.patient', $data);

        }

    }

}

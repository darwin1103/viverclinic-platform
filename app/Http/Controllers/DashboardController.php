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

            $patientCount = User::role('PATIENT')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count();

            $patientList = User::role('PATIENT')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->select(['id', 'name'])
                ->get();

            $branches = Branch::select(['id', 'name'])->get();

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
                ->latest()
                ->take(5)
                ->get();

            // Cálculos Reales
            $ingresoDiario = \App\Models\TreatmentOrder::whereDate('created_at', today())
                ->whereIn('status', ['Pagado', 'Paid', 'Pago completado', 'Aprobado'])
                ->sum('total');

            $egresosHoy = 0; // Todavía no hay tabla de Egresos

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

        }elseif($user->hasRole('PATIENT')){

            if (!Auth::user()->informed_consent) {
                return redirect()->route('client.informed-consent.create');
            }

            $user = Auth::user();

            $contractedTreatments = ContractedTreatment::where('user_id', $user->id)
                ->select(['id'])
                ->get();

            if($contractedTreatments->count() > 1){
                $createAppointmentUrl = route('client.contracted-treatment.index');
            }elseif($contractedTreatments->count() == 1){
                $createAppointmentUrl = route('client.schedule-appointment.index', ['contracted_treatment' =>  $contractedTreatments[0]->id]);
            }else{
                $createAppointmentUrl = null;
            }

            $data = [
                'createAppointmentUrl' => $createAppointmentUrl,
            ];

            return view('dashboards.patient',  $data);

        }

    }

}

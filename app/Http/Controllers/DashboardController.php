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
        if($user->hasRole(['SUPER_ADMIN', 'OWNER'])){

            $patientCount = User::role('PATIENT')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->count();

            $patientList = User::role('PATIENT')
                ->whereDate('created_at', '>=', now()->subDays(7))
                ->select(['id', 'name'])
                ->get();

            $todayAppointments = Appointment::whereBetween('schedule', [
                today()->startOfDay(),
                today()->endOfDay(),
            ])
            ->count();

            $todayAppointmentsList = Appointment::whereBetween('schedule', [
                today()->startOfDay(),
                today()->endOfDay(),
            ])
            ->with([
                'contractedTreatment.user',
                'contractedTreatment.treatment'
            ])
            ->get();

            $branches = Branch::select(['id', 'name'])->get();

            $data = [
                'todayAppointments' => $todayAppointments,
                'todayAppointmentsList' => $todayAppointmentsList,
                'patientCount' => $patientCount,
                'patientList' => $patientList,
                'branches' => $branches,
            ];

            return view('dashboards.admin', $data);

        }elseif($user->hasRole('EMPLOYEE')){

            return view('dashboards.employee');

        }elseif($user->hasRole('PATIENT')){

            if (!Auth::user()->informed_consent) {

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

            return redirect()->route('client.informed-consent.create');

        }

    }

}

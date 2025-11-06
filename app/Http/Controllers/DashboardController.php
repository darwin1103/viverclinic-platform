<?php

namespace App\Http\Controllers;

use App\Models\Treatment;
use App\Models\Branch;
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

            $patientCount = User::role('PATIENT')->count();
            $branches = Branch::select(['id', 'name'])->get();

            $data = [
                'patientCount' => $patientCount,
                'branches' => $branches,
            ];

            return view('dashboards.admin', $data);

        }elseif($user->hasRole('EMPLOYEE')){

            return view('dashboards.employee');

        }elseif($user->hasRole('PATIENT')){

            if (!Auth::user()->informed_consent) {

                return view('dashboards.patient');

            }

            return redirect()->route('client.informed-consent.create');

        }

    }

}

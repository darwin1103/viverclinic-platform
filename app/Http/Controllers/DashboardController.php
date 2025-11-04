<?php

namespace App\Http\Controllers;

use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\ToxicologicalCondition;
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
        if($user->hasRole('SUPER_ADMIN')){

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

            $genres = Gender::where('status', true)->get();
            $documentTypes = DocumentType::where('status', true)->get();
            $pathologicalConditions = PathologicalCondition::where('status', true)->get();
            $toxicologicalConditions = ToxicologicalCondition::where('status', true)->get();
            $gynecoObstetricConditions = GynecoObstetricCondition::where('status', true)->get();
            $medicationConditions = MedicationCondition::where('status', true)->get();
            $dietaryConditions = DietaryCondition::where('status', true)->get();
            $treatments = Treatment::where('active', true)->get();

            $data = [
                'genres' => $genres,
                'documentTypes' => $documentTypes,
                'pathologicalConditions' => $pathologicalConditions,
                'toxicologicalConditions' => $toxicologicalConditions,
                'gynecoObstetricConditions' => $gynecoObstetricConditions,
                'medicationConditions' => $medicationConditions,
                'dietaryConditions' => $dietaryConditions,
                'treatments' => $treatments,
            ];

            return view('client.informed-consent', $data);

        }

    }

}

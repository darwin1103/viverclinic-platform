<?php

namespace App\Http\Controllers;

use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\ToxicologicalCondition;
use App\Models\TreatmentCondition;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
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
        if (Auth::user()->informed_consent) {
            $genres = Gender::where('status',Gender::ACTIVE_STATUS)->get();
            $documentTypes = DocumentType::where('status',DocumentType::ACTIVE_STATUS)->get();
            $pathologicalConditions = PathologicalCondition::where('status',PathologicalCondition::ACTIVE_STATUS)->get();
            $toxicologicalConditions = ToxicologicalCondition::where('status',ToxicologicalCondition::ACTIVE_STATUS)->get();
            $gynecoObstetricConditions = GynecoObstetricCondition::where('status',GynecoObstetricCondition::ACTIVE_STATUS)->get();
            $medicationConditions = MedicationCondition::where('status',MedicationCondition::ACTIVE_STATUS)->get();
            $dietaryConditions = DietaryCondition::where('status',DietaryCondition::ACTIVE_STATUS)->get();
            $treatmentConditions = TreatmentCondition::where('status',TreatmentCondition::ACTIVE_STATUS)->get();
            return view(
                'users.informed-consent',
                compact(
                    'genres',
                    'documentTypes',
                    'pathologicalConditions',
                    'toxicologicalConditions',
                    'gynecoObstetricConditions',
                    'medicationConditions',
                    'dietaryConditions',
                    'treatmentConditions'
                )
            );
        }
        return view('home');
    }
}

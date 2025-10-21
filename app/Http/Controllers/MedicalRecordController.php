<?php

namespace App\Http\Controllers;

use App\Models\DietaryCondition;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\ToxicologicalCondition;
use App\Models\TreatmentCondition;
use Illuminate\Http\Request;

class MedicalRecordController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pathologicalConditions = PathologicalCondition::where('status',PathologicalCondition::ACTIVE_STATUS)->get();
        $toxicologicalConditions = ToxicologicalCondition::where('status',ToxicologicalCondition::ACTIVE_STATUS)->get();
        $gynecoObstetricConditions = GynecoObstetricCondition::where('status',GynecoObstetricCondition::ACTIVE_STATUS)->get();
        $medicationConditions = MedicationCondition::where('status',MedicationCondition::ACTIVE_STATUS)->get();
        $dietaryConditions = DietaryCondition::where('status',DietaryCondition::ACTIVE_STATUS)->get();
        $treatmentConditions = TreatmentCondition::where('status',TreatmentCondition::ACTIVE_STATUS)->get();
        return view(
            'medical-record.index',
            compact(
                'pathologicalConditions',
                'toxicologicalConditions',
                'gynecoObstetricConditions',
                'medicationConditions',
                'dietaryConditions',
                'treatmentConditions'
            )
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}

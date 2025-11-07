<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\ToxicologicalCondition;
use App\Models\Treatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaveInformedConsentController extends Controller
{


    public function create() {

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

    /**
     * Handle the incoming request.
     */
    public function store(Request $request) {

        $request->validate([
            'name' => 'required|string',
            'citizenship' => 'nullable|string',
            'documentType' => 'nullable|exists:document_types,id',
            'documentNumber' => 'nullable|string',
            'email' => 'required|string|email|max:255',
            'birthday' => 'nullable|date',
            'gender' => 'nullable|exists:genres,id',
            'profession' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'pathologicalHistory' => 'nullable|exists:pathological_conditions,id',
            'toxicologicalHistory' => 'nullable|exists:toxicological_conditions,id',
            'gynecoObstetricHistory' => 'nullable|exists:gyneco_obstetric_conditions,id',
            'medications' => 'nullable|exists:medications,id',
            'dietaryHistory' => 'nullable|exists:dietary_conditions,id',
            'treatment' => 'nullable|exists:treatments,id',
            'surgery' => 'nullable|string',
            'recommendation' => 'nullable|string',
        ]);


        $client = User::where('id',Auth::user()->id)->first();
        if (!$client) {
            return redirect()->back()->with('info', 'Operation failed, try again');
        }
        $client->name = $request->name;
        if (isset($request->citizenship)) {
            $client->citizenship = $request->citizenship;
        } else {
            $client->citizenship = null;
        }
        if (isset($request->documentType)) {
            $client->document_type_id = $request->documentType;
        } else {
            $client->document_type_id = null;
        }
        if (isset($request->documentNumber)) {
            $client->document_number = $request->documentNumber;
        } else {
            $client->document_number = null;
        }
        $client->email = $request->email;
        if (isset($request->birthday)) {
            $client->birthday = $request->birthday;
        } else {
            $client->birthday = null;
        }
        if (isset($request->gender)) {
            $client->gender_id = $request->gender;
        } else {
            $client->gender_id = null;
        }
        if (isset($request->profession)) {
            $client->profession = $request->profession;
        } else {
            $client->profession = null;
        }
        if (isset($request->phone)) {
            $client->phone = $request->phone;
        } else {
            $client->phone = null;
        }
        if (isset($request->address)) {
            $client->address = $request->address;
        } else {
            $client->address = null;
        }
        if (isset($request->pathologicalHistory)) {
            $client->pathological_id = $request->pathologicalHistory;
        } else {
            $client->pathological_id = null;
        }
        if (isset($request->toxicologicalHistory)) {
            $client->toxicological_id = $request->toxicologicalHistory;
        } else {
            $client->toxicological_id = null;
        }
        if (isset($request->gynecoObstetricHistory)) {
            $client->gyneco_obstetric_id = $request->gynecoObstetricHistory;
        } else {
            $client->gyneco_obstetric_id = null;
        }
        if (isset($request->medications)) {
            $client->medication_id = $request->medications;
        } else {
            $client->medication_id = null;
        }
        if (isset($request->dietaryHistory)) {
            $client->dietary_id = $request->dietaryHistory;
        } else {
            $client->dietary_id = null;
        }
        if (isset($request->treatment)) {
            $client->treatment_id = $request->treatment;
        } else {
            $client->treatment_id = null;
        }
        if (isset($request->surgery)) {
            $client->surgery = $request->surgery;
        } else {
            $client->surgery = null;
        }
        if (isset($request->recommendation)) {
            $client->recommendation = $request->recommendation;
        } else {
            $client->recommendation = null;
        }
        $client->informed_consent = false;
        $client->save();

        return redirect()->route('client.treatment.index')->with('success', 'Successful operation');

    }

}

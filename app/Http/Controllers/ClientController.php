<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\DietaryCondition;
use App\Models\DocumentType;
use App\Models\Gender;
use App\Models\GynecoObstetricCondition;
use App\Models\MedicationCondition;
use App\Models\PathologicalCondition;
use App\Models\Role;
use App\Models\ToxicologicalCondition;
use App\Models\Treatment;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ClientController extends Controller
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

        $clients = User::select('id','name','created_at','updated_at')
            ->role('PATIENT')
            ->paginate(10);

        $branches = Branch::all();

        return view('client.index', compact('clients', 'branches'));

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('client.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'requestInformedConsent' => 'nullable|string'
        ]);

        // Create new user with password
        $password = Str::random(12);

        $client = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($password),
            'informed_consent' => ($request->requestInformedConsent && $request->requestInformedConsent == "on") ? true : false,
        ]);

        $client->assignRole('PATIENT');

        $client->notify(new UserCreatedNotification($client->name, $client->email, $password));

        return redirect()->back()->with('success', 'User created successfully');

    }

    /**
     * Display the specified resource.
     */
    public function show(User $client)
    {

        $genres = Gender::where('status', true)->get();
        $documentTypes = DocumentType::where('status', true)->get();
        $pathologicalConditions = PathologicalCondition::where('status', true)->get();
        $toxicologicalConditions = ToxicologicalCondition::where('status', true)->get();
        $gynecoObstetricConditions = GynecoObstetricCondition::where('status', true)->get();
        $medicationConditions = MedicationCondition::where('status', true)->get();
        $dietaryConditions = DietaryCondition::where('status', true)->get();
        $treatments = Treatment::where('active', true)->get();

        $data = [
            'client' => $client,
            'genres' => $genres,
            'documentTypes' => $documentTypes,
            'pathologicalConditions' => $pathologicalConditions,
            'toxicologicalConditions' => $toxicologicalConditions,
            'gynecoObstetricConditions' => $gynecoObstetricConditions,
            'medicationConditions' => $medicationConditions,
            'dietaryConditions' => $dietaryConditions,
            'treatments' => $treatments,
        ];

        return view('client.show', $data);

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $client)
    {
        return view('client.edit', compact('client'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $client)
    {

        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'requestInformedConsent' => 'nullable|string'
        ]);

        $client->name = $request->name;
        $client->email = $request->email;
        $client->informed_consent = ($request->requestInformedConsent && $request->requestInformedConsent == "on") ? true : false;
        $client->save();

        return redirect()->back()->with('success', 'Successful operation');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $client)
    {

        $client->delete();

        return redirect()->back()->with('success', 'Successful operation');

    }

    public function getusers(string $roleId) {

        $clients = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', self::SUPER_ADMIN_ROLE_ID);
        })
        ->with('roles')
        ->get();

        $role = Role::where('id', $roleId)->first();

        $response = [];

        foreach ($clients as $key => $client) {

            $response[] = [
                'id' => $client->id,
                'name' => $client->name,
                'contains' => ($client->roles->contains($role)) ? 'checked' : '',
            ];
        }

        return response()->json([
            'users' => $response
        ]);

    }

    public function saveInformedConsent(Request $request) {

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
            'termsConditions' => 'required|string',
            'notPregnant' => 'nullable|string'
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
        if (isset($request->termsConditions) && $request->termsConditions=='on') {
            $client->terms_conditions = true;
        } else {
            $client->terms_conditions = false;
        }
        if (isset($request->notPregnant) && $request->notPregnant=='on') {
            $client->not_pregnant = true;
        } else {
            $client->not_pregnant = false;
        }
        $client->informed_consent = false;
        $client->save();

        // return redirect()->back()->with('success', 'Successful operation'); // ***

        return redirect()->route('buy-package.create')->with('success', 'Successful operation');

    }
    
}

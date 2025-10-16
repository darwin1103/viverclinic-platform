<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use App\Notifications\UserCreatedNotification;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller
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
        $users = User::select('uuid','name','created_at','updated_at')
        ->whereDoesntHave('roles', function ($query) {
            $query->where('id', self::SUPER_ADMIN_ROLE_ID);
        })->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
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
        try {
            $existingUser = User::withTrashed()->where('email', $request->email)->first();
            if ($existingUser) {
                $timestamp = now()->format('Ymd_His');
                $emailParts = explode('@', $existingUser->email);
                if (count($emailParts) === 2) {
                    $newEmail = $emailParts[0] . "_deleted_" . $timestamp . '@' . $emailParts[1];
                } else {
                    $newEmail = $existingUser->email . "_deleted_" . $timestamp;
                }
                $existingUser->update(['email' => $newEmail]);
            }
            $password = Str::random(12);
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($password),
                'informed_consent' => ($request->requestInformedConsent && $request->requestInformedConsent == "on")?true:false
            ]);
            $user->notify(new UserCreatedNotification($user->name,$user->email,$password));
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $uuid)
    {
        $r = [
            'uuid' => $uuid
        ];
        $validator = Validator::make($r, [
            'uuid' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('info', 'Invalid value');
        }
        $user = User::where('uuid',$uuid)->first();
        $roles = Role::where('id','<>', self::SUPER_ADMIN_ROLE_ID)->get();
        return view('users.show', compact('user','roles'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $uuid)
    {
        $r = [
            'uuid' => $uuid
        ];
        $validator = Validator::make($r, [
            'uuid' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('info', 'Invalid value');
        }
        $user = User::where('uuid',$uuid)->first();
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $uuid)
    {
        $r = [
            'uuid' => $uuid
        ];
        $validator = Validator::make($r, [
            'uuid' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('info', 'Invalid value');
        }
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'requestInformedConsent' => 'nullable|string'
        ]);
        try {
            $user = User::where('uuid',$uuid)->first();
            if (!$user) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            $user->name = $request->name;
            $user->email = $request->email;
            $user->informed_consent = ($request->requestInformedConsent && $request->requestInformedConsent == "on")?true:false;
            $user->save();
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $uuid)
    {
        $r = [
            'uuid' => $uuid
        ];
        $validator = Validator::make($r, [
            'uuid' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('info', 'Invalid value');
        }
        try {
            $user = User::where('uuid',$uuid)->first();
            if (!$user) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            $user->delete();
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }

    public function getusers(string $roleUUID) {
        $r = [
            'roleUUID' => $roleUUID
        ];
        $validator = Validator::make($r, [
            'roleUUID' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return response()->json(null,400);
        }
        try {
            $users = User::whereDoesntHave('roles', function ($query) {
                $query->where('id', self::SUPER_ADMIN_ROLE_ID);
            })->paginate(10);
            $role = Role::where('uuid',$roleUUID)->first();
            $response = [];
            foreach ($users as $key => $user) {
                $response[] = [
                    'uuid' => $user->uuid,
                    'name' => $user->name,
                    'contains' => ($user->roles->contains($role))?'checked':''
                ];
            }
            return response()->json([
                'users' => $response
            ]);
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
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
        try {
            $user = User::where('uuid',Auth::user()->uuid)->first();
            if (!$user) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            $user->name = $request->name;
            if (isset($request->citizenship)) {
                $user->citizenship = $request->citizenship;
            } else {
                $user->citizenship = null;
            }
            if (isset($request->documentType)) {
                $user->document_type_id = $request->documentType;
            } else {
                $user->document_type_id = null;
            }
            if (isset($request->documentNumber)) {
                $user->document_number = $request->documentNumber;
            } else {
                $user->document_number = null;
            }
            $user->email = $request->email;
            if (isset($request->birthday)) {
                $user->birthday = $request->birthday;
            } else {
                $user->birthday = null;
            }
            if (isset($request->gender)) {
                $user->gender_id = $request->gender;
            } else {
                $user->gender_id = null;
            }
            if (isset($request->profession)) {
                $user->profession = $request->profession;
            } else {
                $user->profession = null;
            }
            if (isset($request->phone)) {
                $user->phone = $request->phone;
            } else {
                $user->phone = null;
            }
            if (isset($request->address)) {
                $user->address = $request->address;
            } else {
                $user->address = null;
            }
            if (isset($request->pathologicalHistory)) {
                $user->pathological_id = $request->pathologicalHistory;
            } else {
                $user->pathological_id = null;
            }
            if (isset($request->toxicologicalHistory)) {
                $user->toxicological_id = $request->toxicologicalHistory;
            } else {
                $user->toxicological_id = null;
            }
            if (isset($request->gynecoObstetricHistory)) {
                $user->gyneco_obstetric_id = $request->gynecoObstetricHistory;
            } else {
                $user->gyneco_obstetric_id = null;
            }
            if (isset($request->medications)) {
                $user->medication_id = $request->medications;
            } else {
                $user->medication_id = null;
            }
            if (isset($request->dietaryHistory)) {
                $user->dietary_id = $request->dietaryHistory;
            } else {
                $user->dietary_id = null;
            }
            if (isset($request->treatment)) {
                $user->treatment_id = $request->treatment;
            } else {
                $user->treatment_id = null;
            }
            if (isset($request->surgery)) {
                $user->surgery = $request->surgery;
            } else {
                $user->surgery = null;
            }
            if (isset($request->recommendation)) {
                $user->recommendation = $request->recommendation;
            } else {
                $user->recommendation = null;
            }
            if (isset($request->termsConditions) && $request->termsConditions=='on') {
                $user->terms_conditions = true;
            } else {
                $user->terms_conditions = false;
            }
            if (isset($request->notPregnant) && $request->notPregnant=='on') {
                $user->not_pregnant = true;
            } else {
                $user->not_pregnant = false;
            }
            // $user->informed_consent = false;
            $user->save();
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }
    
}

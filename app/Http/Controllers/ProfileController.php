<?php

namespace App\Http\Controllers;

use App\Models\Gender;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ProfileController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('profile.index');
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
    public function update(Request $request, string $uuid)
    {
        $r = [
            'uuid' => $uuid
        ];
        $validator = Validator::make($r, [
            'uuid' => 'required|uuid',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid value');
        }
        $request->validate([
            'name' => 'required|string',
            'birthday' => 'nullable|date|before:today',
            'genderSelect' => 'required|not_in:-1'
        ]);
        try {
            $user = User::where('uuid',$uuid)->first();
            if (!$user) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            $user->name = $request->input('name');
            if (isset($request->birthday) && !empty($request->birthday)) {
                $user->birthday = $request->birthday;
            }
            if(isset($request->genderSelect) && !empty($request->genderSelect) && $request->genderSelect != '-1') {
                $gender = Gender::where('code',$request->genderSelect)->first();
                $user->gender_id = $gender->id;
            }
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
            'uuid' => 'required|uuid',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Invalid value');
        }
        try {
            $user = User::where('uuid',$uuid)->first();
            if (!$user) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            if ($user->hasRole('SUPER_ADMIN')) {
                return redirect()->back()->with('info', 'Administrator users cannot be deleted from the interface');
            } else {
                if ($user->photo_profile) {
                    Storage::disk('public')->delete($user->photo_profile);
                }
                $user->delete();
                Auth::logout();
                return redirect('/login')->with('success', 'Account deleted successfully');
            }
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }

    public function uploadProfilePhoto(Request $request) {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,jpg,png|max:2048'
        ]);
        try {
            $user = User::where('uuid',Auth::user()->uuid)->first();
            if ($request->hasFile('image')) {
                if (!$user->directory) {
                    $dirName = Str::uuid()->toString();
                    Storage::disk('public')->makeDirectory($dirName);
                    $user->directory = $dirName;
                    $user->save();
                }
                if ($user->photo_profile) {
                    Storage::disk('public')->delete($user->photo_profile);
                }
                $user->photo_profile = $request->file('image')->store($user->directory,'public');
                $user->save();
            }
            return response()->json([
                'profileURL' => ($user->photo_profile)?asset(Storage::url($user->photo_profile)):null
            ]);
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
    }
}

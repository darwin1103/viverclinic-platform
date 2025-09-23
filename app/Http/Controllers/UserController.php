<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::whereDoesntHave('roles', function ($query) {
            $query->where('id', self::SUPER_ADMIN_ROLE_ID);
        })->paginate(10);
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::where('id','<>', self::SUPER_ADMIN_ROLE_ID)->get();
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|max:255',
            'roleSelect' => 'required'
        ]);
        try {
            
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', 'Something went wrong, please try again, if the problem persists, please report it to administrator');
        }
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

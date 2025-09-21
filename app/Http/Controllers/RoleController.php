<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
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
        $roles = Role::paginate(10);
        return view('roles.index', compact('roles'));
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
        $request->validate([
            'roleName' => 'required|string'
        ]);
        try {
            Role::create([
                'name' => $request->roleName
            ]);
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', 'Something went wrong, please try again, if the problem persists, please report it to administrator');
        }
    }

    public function assignPermission(Request $request) {
        $request->validate([
            'roleUUId' => 'required|uuid',
            'permissionUUId' => 'required|uuid'
        ]);
        try {
            $role = Role::where('uuid',$request->roleUUId)->first();
            $permission = Permission::where('uuid',$request->permissionUUId)->first();
            $role->givePermissionTo($permission);
            return response()->json();
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
    }

    public function removePermission(Request $request) {
        $request->validate([
            'roleUUId' => 'required|uuid',
            'permissionUUId' => 'required|uuid'
        ]);
        try {
            $role = Role::where('uuid',$request->roleUUId)->first();
            $permission = Permission::where('uuid',$request->permissionUUId)->first();
            $role->revokePermissionTo($permission);
            return response()->json();
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
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
    public function destroy(string $uuid)
    {
        $r = [
            'uuid' => $uuid
        ];
        $validator = Validator::make($r, [
            'uuid' => 'required|uuid',
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('info', 'Invalid value');
        }
        try {
            $role = Role::where('uuid',$uuid)->first();
            if (!$role) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            $role->delete();
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', 'Something went wrong, please try again, if the problem persists, please report it to administrator');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
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
        $roles = Role::where('id','<>', self::SUPER_ADMIN_ROLE_ID)->paginate(10);
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
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }

    public function assignPermission(Request $request) {
        $request->validate([
            'roleId' => 'required',
            'permissionId' => 'required'
        ]);
        try {
            $role = Role::where('id',$request->roleId)->first();
            $permission = Permission::where('id',$request->permissionId)->first();
            $role->givePermissionTo($permission);
            return response()->json();
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
    }

    public function removePermission(Request $request) {
        $request->validate([
            'roleId' => 'required',
            'permissionId' => 'required'
        ]);
        try {
            $role = Role::where('id',$request->roleId)->first();
            $permission = Permission::where('id',$request->permissionId)->first();
            $role->revokePermissionTo($permission);
            return response()->json();
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
    }

    public function assignUser(Request $request) {
        $request->validate([
            'roleId' => 'required',
            'userId' => 'required'
        ]);
        try {
            $role = Role::where('id',$request->roleId)->first();
            $user = User::where('id',$request->userId)->first();
            $user->assignRole($role);
            return response()->json();
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
    }

    public function removeUser(Request $request) {
        $request->validate([
            'roleId' => 'required',
            'userId' => 'required'
        ]);
        try {
            $role = Role::where('id',$request->roleId)->first();
            $user = User::where('id',$request->userId)->first();
            $user->removeRole($role);
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
    public function destroy(Role $role)
    {

        $role->delete();

        return redirect()->back()->with('success', 'Successful operation');

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use App\Models\Role;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
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
        //
    }

    public function getPermissionsList(string $roleId) {

        $permissions = Permission::with('roles')->get();

        $role = Role::where('id', $roleId)->first();

        $response = [];

        foreach ($permissions as $key => $permission) {
            $response[] = [
                'id' => $permission->id,
                'name' => $permission->name,
                'contains' => ($permission->roles->contains($role)) ? 'checked' : '',
            ];
        }

        return response()->json([
            'permissions' => $response
        ]);

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

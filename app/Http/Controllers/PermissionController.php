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

    public function getPermissionsList(string $roleUUID) {
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
            $permissions = Permission::all();
            $role = Role::where('uuid',$roleUUID)->first();
            $response = [];
            foreach ($permissions as $key => $p) {
                $response[] = [
                    'uuid' => $p->uuid,
                    'name' => $p->name,
                    'contains' => ($p->roles->contains($role))?'checked':''
                ];
            }
            return response()->json([
                'permissions' => $response
            ]);
        } catch (Exception $e) {
            logger($e);
            return response()->json(null,500);
        }
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

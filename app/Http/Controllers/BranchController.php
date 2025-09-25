<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
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
        $branches = Branch::paginate(10);
        return view('branches.index', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('branches.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'required|string',
            'telephone' => 'nullable|string'
        ]);
        try {
            Branch::create([
                'name' => $request->name,
                'address' => $request->address,
                'phone' => $request->telephone
            ]);
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
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
            'uuid' => 'required|uuid'
        ]);
        if ($validator->fails()) {
            return redirect()->back()->with('info', 'Invalid value');
        }
        try {
            $branch = Branch::where('uuid',$uuid)->first();
            if (!$branch) {
                return redirect()->back()->with('info', 'Operation failed, try again');
            }
            $branch->delete();
            return redirect()->back()->with('success', 'Successful operation');
        } catch (Exception $e) {
            logger($e);
            return redirect()->back()->with('error', self::ERROR_GENERAL_MSG);
        }
    }
}

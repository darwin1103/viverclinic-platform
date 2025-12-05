<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Traits\FileUploadTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class BranchController extends Controller
{

    use FileUploadTrait;

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
    public function index(Request $request)
    {
        $branches = Branch::paginate(100);

        if ($request->filled('branch_id')) {
            session(['selected_branch_id' => $request->input('branch_id')]);
        }
        $selectedBranchID = session('selected_branch_id', '');
        return view('admin.branch.index', compact('branches', 'selectedBranchID'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.branch.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $messages = [
            'name.required'             => 'El nombre es obligatorio.',
            'name.string'               => 'El nombre debe ser texto.',

            'address.required'          => 'La dirección es obligatoria.',
            'address.string'            => 'La dirección debe ser texto.',

            'phone.string'              => 'El teléfono debe ser texto.', // O 'nullable|string|max:20', etc., según sea necesario.

            'google_maps_url.string'    => 'La URL de Google Maps debe ser texto.',

            'logo.image'                => 'El archivo de logo debe ser una imagen.',
            'logo.mimes'                => 'El logo debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'logo.max'                  => 'El logo no debe pesar más de :max kilobytes (2 MB).',
            // Nota: 'nullable' no necesita mensaje de error para 'required'.
        ];

        $attributes = [
            'name'            => 'Nombre',
            'address'         => 'Dirección',
            'phone'           => 'Teléfono',
            'google_maps_url' => 'URL de Google Maps',
            'logo'            => 'Logo',
        ];

        $validated = $request->validate([
            'name'            => 'required|string',
            'address'         => 'required|string',
            'phone'           => 'nullable|string',
            'google_maps_url' => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], $messages, $attributes);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->uploadFile($request->file('logo'), 'branches');
        }

        Branch::create($validated);

        return redirect()->back()->with('success', 'Successful operation');

    }

    /**
     * Display the specified resource.
     */
    public function show(Branch $branch)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Branch $branch)
    {
        return view('admin.branch.edit', compact('branch'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Branch $branch)
    {
        $messages = [
            'name.required'             => 'El nombre es obligatorio.',
            'name.string'               => 'El nombre debe ser texto.',

            'address.required'          => 'La dirección es obligatoria.',
            'address.string'            => 'La dirección debe ser texto.',

            'phone.string'              => 'El teléfono debe ser texto.', // O 'nullable|string|max:20', etc., según sea necesario.

            'google_maps_url.string'    => 'La URL de Google Maps debe ser texto.',

            'logo.image'                => 'El archivo de logo debe ser una imagen.',
            'logo.mimes'                => 'El logo debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'logo.max'                  => 'El logo no debe pesar más de :max kilobytes (2 MB).',
            // Nota: 'nullable' no necesita mensaje de error para 'required'.
        ];

        $attributes = [
            'name'            => 'Nombre',
            'address'         => 'Dirección',
            'phone'           => 'Teléfono',
            'google_maps_url' => 'URL de Google Maps',
            'logo'            => 'Logo',
        ];

        $validated = $request->validate([
            'name'            => 'required|string',
            'address'         => 'required|string',
            'phone'           => 'nullable|string',
            'google_maps_url' => 'nullable|string',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ], $messages, $attributes);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $this->uploadFile($request->file('logo'), 'branches', $branch->logo);
        }

        $branch->update($validated);

        return redirect()->route('admin.branch.index')->with('success', 'Branch updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Branch $branch)
    {
        if ($branch->logo) {
            Storage::disk('public')->delete($branch->logo);
        }
        $branch->delete();
        return redirect()->back()->with('success', 'Successful operation');
    }
}

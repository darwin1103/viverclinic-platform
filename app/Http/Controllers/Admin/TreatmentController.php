<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Treatment;
use App\Models\Branch;
use Illuminate\Http\Request;
use App\Traits\FileUploadTrait;
use Illuminate\Support\Facades\Storage;

class TreatmentController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        // Iniciar la consulta base con eager loading para optimizar
        $query = Treatment::with('branches');

        // 1. Filtro de búsqueda por nombre
        if ($request->filled('search')) {
            $query->where('name', 'LIKE', '%' . $request->input('search') . '%');
        }

        // 2. Filtro por estado (activo/inactivo)
        if ($request->filled('active')) {
            if ($request->input('active') === 'active') {
                $query->where('active', true);
            } elseif ($request->input('active') === 'inactive') {
                $query->where('active', false);
            }
            // Si es 'all' o cualquier otro valor, no se aplica filtro de estado
        }

        // 3. Filtro por sucursal
        if ($request->filled('branch_id')) {
            $query->whereHas('branches', function ($branchQuery) use ($request) {
                $branchQuery->where('branches.id', $request->input('branch_id'));
            });
        }

        // Obtener los resultados paginados
        // withQueryString() es crucial para que los links de paginación mantengan los filtros
        $treatments = $query->latest()->paginate(10)->withQueryString();

        // Las sucursales se necesitan para el selector del header y los filtros
        $branches = Branch::all();

        return view('admin.treatment.index', compact('treatments', 'branches'));
    }

     public function create()
    {
        $branches = Branch::all();
        return view('admin.treatment.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:treatments,name',
            'description' => 'required|string',
            'sessions' => 'required|integer|min:1',
            'days_between_sessions' => 'required|integer|min:0',
            'active' => 'sometimes|boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'branches' => 'nullable|array',
            'branches.*.price' => 'required_with:branches.*.attach|nullable|numeric|min:0',
            'branches.*.attach' => 'sometimes|accepted',
        ]);

        $treatmentData = $request->except(['_token', 'branches']);
        $treatmentData['active'] = $request->has('active');

        if ($request->hasFile('main_image')) {
            $treatmentData['main_image'] = $this->uploadFile($request->file('main_image'), 'treatments');
        }

        $treatment = Treatment::create($treatmentData);

        // Sincronizar las relaciones con sucursales
        $syncData = [];
        if (isset($validated['branches'])) {
            foreach ($validated['branches'] as $branchId => $data) {
                // Solo sincronizar si el checkbox 'attach' está marcado
                if (isset($data['attach'])) {
                    $syncData[$branchId] = ['price' => $data['price'] ?? 0];
                }
            }
        }

        $treatment->branches()->sync($syncData);

        return redirect()->route('admin.treatment.index')->with('success', 'Tratamiento creado con éxito.');
    }

    public function show(Treatment $treatment)
    {
        return redirect()->route('admin.treatment.edit', $treatment);
    }

    public function edit(Treatment $treatment)
    {
        $branches = Branch::all();
        // Cargar las sucursales asociadas para un acceso fácil en la vista
        $treatment->load('branches');
        return view('admin.treatment.edit', compact('treatment', 'branches'));
    }

    public function update(Request $request, Treatment $treatment)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'sessions' => 'required|integer|min:1',
            'days_between_sessions' => 'required|integer|min:0',
            'active' => 'sometimes|boolean',
            'main_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'branches' => 'nullable|array',
            'branches.*.price' => 'required_with:branches.*.attach|nullable|numeric|min:0',
            'branches.*.attach' => 'sometimes|accepted',
        ]);

        $treatmentData = $request->except(['_token', '_method', 'branches']);
        $treatmentData['active'] = $request->has('active');

        if ($request->hasFile('main_image')) {
            $treatmentData['main_image'] = $this->uploadFile($request->file('main_image'), 'treatments', $treatment->main_image);
        }

        $treatment->update($treatmentData);

        // Sincronizar las relaciones con sucursales
        $syncData = [];
        if (isset($validated['branches'])) {
            foreach ($validated['branches'] as $branchId => $data) {
                // Solo sincronizar si el checkbox 'attach' está marcado
                if (isset($data['attach'])) {
                    $syncData[$branchId] = ['price' => $data['price'] ?? 0];
                }
            }
        }
        $treatment->branches()->sync($syncData);

        return redirect()->route('admin.treatment.index')->with('success', 'Tratamiento actualizado con éxito.');
    }

    public function destroy(Treatment $treatment)
    {
        if ($treatment->main_image) {
            Storage::disk('public')->delete($treatment->main_image);
        }
        $treatment->delete();
        return redirect()->route('admin.treatment.index')->with('success', 'Tratamiento eliminado con éxito.');
    }
}

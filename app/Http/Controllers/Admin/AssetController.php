<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetNote;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $query = Asset::with('branch');

        // Filtro Search
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro Branch (desde el header)
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $assets = $query->latest()->paginate(10);

        if ($request->ajax()) {
            return view('admin.assets.partials.table-rows', compact('assets'))->render();
        }

        $branches = Branch::all();

        if ($request->filled('branch_id')) {
            session(['selected_branch_id' => $request->input('branch_id')]);
        }
        $selectedBranchID = session('selected_branch_id', '');

        return view('admin.assets.index', compact('assets', 'branches', 'selectedBranchID'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.assets.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
            'stock' => 'required|integer|min:0',
        ], [
            'name.required' => 'El nombre del activo es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists' => 'La sucursal seleccionada no es válida.',
            'stock.required' => 'El stock es obligatorio.',
            'stock.integer' => 'El stock debe ser un número entero.',
            'stock.min' => 'El stock no puede ser menor a cero.',
        ]);

        Asset::create($request->all());

        return redirect()->route('admin.assets.index')->with('success', 'Activo creado correctamente.');
    }

    public function edit(Asset $asset)
    {
        $branches = Branch::all();
        $asset->load('notes.user'); // Cargar notas y usuario
        return view('admin.assets.edit', compact('asset', 'branches'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'branch_id' => 'required|exists:branches,id',
        ], [
            'name.required' => 'El nombre del activo es obligatorio.',
            'name.string' => 'El nombre debe ser un texto válido.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',
            'branch_id.required' => 'Debe seleccionar una sucursal.',
            'branch_id.exists' => 'La sucursal seleccionada no es válida.',
        ]);

        // Nota: El stock no se actualiza aquí directamente, se usa el modal de stock,
        // pero permitimos cambiar otros datos.
        $asset->update($request->only(['name', 'branch_id']));

        return redirect()->route('admin.assets.index')->with('success', 'Activo actualizado correctamente.');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return response()->json(['success' => true, 'message' => 'Activo eliminado.']);
    }

    // --- Métodos de Stock ---

    public function updateStock(Request $request, Asset $asset)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'operation' => 'required|in:add,remove',
            'note' => 'required|string|min:3'
        ], [
            'quantity.required' => 'La cantidad es obligatoria.',
            'quantity.integer' => 'La cantidad debe ser un número entero.',
            'quantity.min' => 'La cantidad a modificar debe ser al menos 1.',
            'operation.required' => 'El tipo de operación es obligatorio.',
            'operation.in' => 'La operación seleccionada no es válida.',
            'note.required' => 'La nota del movimiento es obligatoria.',
            'note.string' => 'La nota debe ser un texto válido.',
            'note.min' => 'La nota debe tener al menos 3 caracteres.',
        ]);

        $currentStock = $asset->stock;
        $quantity = $request->quantity;

        if ($request->operation === 'remove') {
            if ($currentStock - $quantity < 0) {
                return response()->json(['success' => false, 'message' => 'El stock no puede ser menor a cero.'], 422);
            }
            $newStock = $currentStock - $quantity;
            $notePrefix = "Stock reducido en $quantity. ";
        } else {
            $newStock = $currentStock + $quantity;
            $notePrefix = "Stock aumentado en $quantity. ";
        }

        DB::transaction(function () use ($asset, $newStock, $request, $notePrefix) {
            // Actualizar Stock
            $asset->update(['stock' => $newStock]);

            // Crear Nota
            AssetNote::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'content' => $notePrefix . $request->note
            ]);
        });

        return response()->json(['success' => true, 'message' => 'Stock actualizado y nota creada.']);
    }

    // --- Métodos de Notas ---

    public function storeNote(Request $request, Asset $asset)
    {
        $request->validate(['content' => 'required|string'], [
            'content.required' => 'El contenido de la nota es obligatorio.',
            'content.string' => 'El contenido debe ser un texto válido.',
        ]);

        AssetNote::create([
            'asset_id' => $asset->id,
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);

        return back()->with('success', 'Nota agregada.');
    }

    public function updateNote(Request $request, AssetNote $note)
    {
        $request->validate(['content' => 'required|string'], [
            'content.required' => 'El contenido de la nota es obligatorio.',
            'content.string' => 'El contenido debe ser un texto válido.',
        ]);
        $note->update(['content' => $request->content]);
        return back()->with('success', 'Nota actualizada.');
    }

    public function destroyNote(AssetNote $note)
    {
        $note->delete();
        return back()->with('success', 'Nota eliminada.');
    }
}

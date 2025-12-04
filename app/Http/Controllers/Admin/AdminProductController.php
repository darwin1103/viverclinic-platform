<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('branch');

        // Filtro por nombre
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por sucursal
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        $products = $query->latest()->paginate(10);

        $branches = Branch::all();

        $selectedBranchID = $request->input('branch_id') ?? '';

        // Si es una petición fetch (ajax), retornamos solo la tabla
        if ($request->ajax()) {
            return view('admin.products.partials.table', compact('products', 'branches', 'selectedBranchID'))->render();
        }

        return view('admin.products.index', compact('products', 'branches', 'selectedBranchID'));
    }

    public function create()
    {
        $branches = Branch::all();
        return view('admin.products.create', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0|decimal:0,2',
            'branch_id' => 'required|exists:branches,id',
        ]);

        Product::create($validated);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $branches = Branch::all();
        return view('admin.products.edit', compact('product', 'branches'));
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0|decimal:0,2',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $product->update($validated);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado correctamente.');
    }
}

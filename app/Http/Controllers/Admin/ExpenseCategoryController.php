<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    /**
     * Get all categories as JSON.
     */
    public function index()
    {
        return response()->json(ExpenseCategory::orderBy('name')->get());
    }

    /**
     * Store a new category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name',
        ]);

        ExpenseCategory::create($validated);

        return back()->with('success', 'Categoría creada exitosamente.');
    }

    /**
     * Update a category.
     */
    public function update(Request $request, ExpenseCategory $expense_category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:expense_categories,name,' . $expense_category->id,
        ]);

        $expense_category->update($validated);

        return back()->with('success', 'Categoría actualizada exitosamente.');
    }

    /**
     * Delete a category.
     */
    public function destroy(ExpenseCategory $expense_category)
    {
        $expense_category->delete();
        return back()->with('success', 'Categoría eliminada exitosamente.');
    }
}

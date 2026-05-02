<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RecomentationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recommendations = \App\Models\Recommendation::latest()->get();
        return view('recomentations.index', compact('recommendations'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $branchId = session('branch_id');
        if (!$branchId && $request->user()) {
            $branchId = $request->user()->adminsBranches()->first()->id ?? null;
        }

        \App\Models\Recommendation::create([
            'branch_id' => $branchId,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('recomentations.index')->with('success', 'Recomendación creada exitosamente.');
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $recommendation = \App\Models\Recommendation::findOrFail($id);
        $recommendation->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('recomentations.index')->with('success', 'Recomendación actualizada exitosamente.');
    }

    public function destroy(string $id)
    {
        $recommendation = \App\Models\Recommendation::findOrFail($id);
        $recommendation->delete();
        
        return redirect()->route('recomentations.index')->with('success', 'Recomendación eliminada exitosamente.');
    }
}

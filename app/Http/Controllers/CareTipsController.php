<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CareTipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careTips = \App\Models\CareTip::latest()->get();
        return view('care-tips.index', compact('careTips'));
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

        \App\Models\CareTip::create([
            'branch_id' => $branchId,
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('care-tips.index')->with('success', 'Tip de cuidado creado exitosamente.');
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

        $careTip = \App\Models\CareTip::findOrFail($id);
        $careTip->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return redirect()->route('care-tips.index')->with('success', 'Tip de cuidado actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $careTip = \App\Models\CareTip::findOrFail($id);
        $careTip->delete();
        
        return redirect()->route('care-tips.index')->with('success', 'Tip de cuidado eliminado exitosamente.');
    }
}

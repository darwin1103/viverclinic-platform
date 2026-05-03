<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class CareTipsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $careTips = \App\Models\CareTip::latest()->get();
        return view('admin.care-tips.index', compact('careTips'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('care_tips', 'public');
        }

        $branchId = session('branch_id');
        if (!$branchId && $request->user()) {
            $branchId = $request->user()->adminsBranches()->first()->id ?? null;
        }

        \App\Models\CareTip::create([
            'branch_id' => $branchId,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.care-tips.index')->with('success', 'Tip de cuidado creado exitosamente.');
    }

    public function show(string $id)
    {
        $careTip = \App\Models\CareTip::findOrFail($id);
        return view('admin.care-tips.show', compact('careTip'));
    }

    public function edit(string $id)
    {
        $careTip = \App\Models\CareTip::findOrFail($id);
        return view('admin.care-tips.edit', compact('careTip'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $careTip = \App\Models\CareTip::findOrFail($id);

        $imagePath = $careTip->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('care_tips', 'public');
        }

        $careTip->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.care-tips.index')->with('success', 'Tip de cuidado actualizado exitosamente.');
    }

    public function destroy(string $id)
    {
        $careTip = \App\Models\CareTip::findOrFail($id);
        if ($careTip->image && Storage::disk('public')->exists($careTip->image)) {
            Storage::disk('public')->delete($careTip->image);
        }
        $careTip->delete();
        
        return redirect()->route('admin.care-tips.index')->with('success', 'Tip de cuidado eliminado exitosamente.');
    }
}

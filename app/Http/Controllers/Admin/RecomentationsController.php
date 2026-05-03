<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

use Illuminate\Http\Request;

class RecomentationsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $recommendations = \App\Models\Recommendation::latest()->get();
        return view('admin.recomentations.index', compact('recommendations'));
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
            $imagePath = $request->file('image')->store('recommendations', 'public');
        }

        $branchId = session('branch_id');
        if (!$branchId && $request->user()) {
            $branchId = $request->user()->adminsBranches()->first()->id ?? null;
        }

        \App\Models\Recommendation::create([
            'branch_id' => $branchId,
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.recomentations.index')->with('success', 'Recomendación creada exitosamente.');
    }

    public function show(string $id)
    {
        $recommendation = \App\Models\Recommendation::findOrFail($id);
        return view('admin.recomentations.show', compact('recommendation'));
    }

    public function edit(string $id)
    {
        $recommendation = \App\Models\Recommendation::findOrFail($id);
        return view('admin.recomentations.edit', compact('recommendation'));
    }

    public function update(Request $request, string $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $recommendation = \App\Models\Recommendation::findOrFail($id);

        $imagePath = $recommendation->image;
        if ($request->hasFile('image')) {
            if ($imagePath && Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
            $imagePath = $request->file('image')->store('recommendations', 'public');
        }

        $recommendation->update([
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'content' => $request->content,
        ]);

        return redirect()->route('admin.recomentations.index')->with('success', 'Recomendación actualizada exitosamente.');
    }

    public function destroy(string $id)
    {
        $recommendation = \App\Models\Recommendation::findOrFail($id);
        if ($recommendation->image && Storage::disk('public')->exists($recommendation->image)) {
            Storage::disk('public')->delete($recommendation->image);
        }
        $recommendation->delete();
        
        return redirect()->route('admin.recomentations.index')->with('success', 'Recomendación eliminada exitosamente.');
    }
}

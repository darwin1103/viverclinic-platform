<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PromotionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $promotions = \App\Models\Promotion::with(['treatment', 'package.branch', 'branch'])->latest()->get();
        return view('admin.promotions.index', compact('promotions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $branches = \App\Models\Branch::orderBy('name')->get();
        $treatments = \App\Models\Treatment::where('active', true)->orderBy('name')->get();
        return view('admin.promotions.create', compact('branches', 'treatments'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): \Illuminate\Http\RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id',
            'treatment_id' => 'required|exists:treatments,id',
            'branch_treatment_id' => 'required|exists:branch_treatment,id',
            'discount_type' => 'required|in:percentage,fixed',
            'discount' => 'required|numeric|min:0',
            'activation_mode' => 'required|in:scheduled,manual',
            'start_date' => 'required_if:activation_mode,scheduled|nullable|date',
            'end_date' => 'required_if:activation_mode,scheduled|nullable|date|after_or_equal:start_date',
        ]);

        // If activation mode is scheduled, set default is_active to false
        $validated['is_active'] = false;

        \App\Models\Promotion::create($validated);

        return redirect()->route('admin.promotions.index')->with('success', 'Promoción creada exitosamente.');
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
    public function edit(string $id): View
    {
        $promotion = \App\Models\Promotion::findOrFail($id);
        $branches = \App\Models\Branch::orderBy('name')->get();
        $treatments = \App\Models\Treatment::where('active', true)->orderBy('name')->get();
        $packages = \App\Models\BranchTreatment::with('branch')
            ->where('treatment_id', $promotion->treatment_id)
            ->where('branch_id', $promotion->branch_id)
            ->get();

        return view('admin.promotions.edit', compact('promotion', 'branches', 'treatments', 'packages'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id): \Illuminate\Http\RedirectResponse
    {
        $promotion = \App\Models\Promotion::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'branch_id' => 'required|exists:branches,id',
            'treatment_id' => 'required|exists:treatments,id',
            'branch_treatment_id' => 'required|exists:branch_treatment,id',
            'discount_type' => 'required|in:percentage,fixed',
            'discount' => 'required|numeric|min:0',
            'activation_mode' => 'required|in:scheduled,manual',
            'start_date' => 'required_if:activation_mode,scheduled|nullable|date',
            'end_date' => 'required_if:activation_mode,scheduled|nullable|date|after_or_equal:start_date',
        ]);

        // Reset fields when switching mode
        if ($validated['activation_mode'] === 'manual') {
            $validated['start_date'] = null;
            $validated['end_date'] = null;
        } else {
            $validated['is_active'] = false;
        }

        $promotion->update($validated);

        return redirect()->route('admin.promotions.index')->with('success', 'Promoción actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): \Illuminate\Http\RedirectResponse
    {
        $promotion = \App\Models\Promotion::findOrFail($id);
        $promotion->delete();

        return redirect()->route('admin.promotions.index')->with('success', 'Promoción eliminada exitosamente.');
    }

    /**
     * Toggle active state for manual promotions.
     */
    public function toggleActive(string $id): \Illuminate\Http\RedirectResponse
    {
        $promotion = \App\Models\Promotion::findOrFail($id);
        
        if ($promotion->activation_mode === 'manual') {
            $promotion->is_active = !$promotion->is_active;
            $promotion->save();
            return redirect()->back()->with('success', 'Estado de la promoción actualizado exitosamente.');
        }

        return redirect()->back()->with('error', 'No se puede activar manualmente una promoción programada.');
    }

    /**
     * Get packages for a given treatment via AJAX.
     */
    public function getPackages(Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'treatment_id' => 'required|exists:treatments,id',
            'branch_id' => 'required|exists:branches,id',
        ]);

        $packages = \App\Models\BranchTreatment::with('branch')
            ->where('treatment_id', $request->treatment_id)
            ->where('branch_id', $request->branch_id)
            ->get()
            ->map(function ($package) {
                return [
                    'id' => $package->id,
                    'name' => $package->name,
                    'price' => (float) $package->price,
                    'branch_name' => $package->branch ? $package->branch->name : 'General'
                ];
            });

        return response()->json($packages);
    }
}

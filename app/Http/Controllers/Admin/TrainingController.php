<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index()
    {
        $trainings = Training::latest()->get();
        return view('admin.trainings.index', compact('trainings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'youtube_url' => 'nullable|url',
        ]);

        Training::create($request->all());

        return redirect()->route('admin.trainings.index')->with('success', 'Capacitación creada exitosamente.');
    }

    public function update(Request $request, Training $training)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'youtube_url' => 'nullable|url',
        ]);

        $training->update($request->all());

        return redirect()->route('admin.trainings.index')->with('success', 'Capacitación actualizada exitosamente.');
    }

    public function destroy(Training $training)
    {
        $training->delete();
        return redirect()->route('admin.trainings.index')->with('success', 'Capacitación eliminada exitosamente.');
    }
}

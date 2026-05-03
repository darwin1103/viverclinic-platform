<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Training;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:SUPER_ADMIN|OWNER')->except(['index', 'show']);
    }

    public function index()
    {
        $trainings = Training::latest()->get();
        return view('admin.trainings.index', compact('trainings'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
            'youtube_url' => 'nullable|url',
        ]);

        Training::create($request->all());

        return redirect()->route('admin.trainings.index')->with('success', 'Capacitación creada exitosamente.');
    }

    public function show(Training $training)
    {
        return view('admin.trainings.show', compact('training'));
    }

    public function edit(Training $training)
    {
        return view('admin.trainings.edit', compact('training'));
    }

    public function update(Request $request, Training $training)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|max:500',
            'content' => 'required|string',
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

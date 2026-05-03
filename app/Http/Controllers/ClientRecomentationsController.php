<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientRecomentationsController extends Controller
{
    public function index()
    {
        $recommendations = \App\Models\Recommendation::latest()->get();
        return view('recomentations.index', compact('recommendations'));
    }

    public function show($id)
    {
        $recommendation = \App\Models\Recommendation::findOrFail($id);
        return view('recomentations.show', compact('recommendation'));
    }
}

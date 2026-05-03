<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ClientCareTipsController extends Controller
{
    public function index()
    {
        $careTips = \App\Models\CareTip::latest()->get();
        return view('care-tips.index', compact('careTips'));
    }

    public function show($id)
    {
        $careTip = \App\Models\CareTip::findOrFail($id);
        return view('care-tips.show', compact('careTip'));
    }
}

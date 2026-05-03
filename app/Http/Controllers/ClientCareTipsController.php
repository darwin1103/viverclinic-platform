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
}

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
}

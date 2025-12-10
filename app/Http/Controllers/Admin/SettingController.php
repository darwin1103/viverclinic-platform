<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $wompiPublicKey = Setting::get('wompi_public_key');
        $wompiIntegritySecret = Setting::get('wompi_integrity_secret');

        return view('admin.settings.index', compact('wompiPublicKey', 'wompiIntegritySecret'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wompi_public_key' => 'nullable|string',
            'wompi_integrity_secret' => 'nullable|string',
        ]);

        Setting::updateOrCreate(['key' => 'wompi_public_key'], ['value' => $request->wompi_public_key]);
        Setting::updateOrCreate(['key' => 'wompi_integrity_secret'], ['value' => $request->wompi_integrity_secret]);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}

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

        // Configuración de referidos
        $referralEnabled = Setting::get('referral_enabled', '1');
        $referralBonusSessions = Setting::get('referral_bonus_sessions', '3');
        $referralCommissionType = Setting::get('referral_commission_type', 'fixed');
        $referralCommissionValue = Setting::get('referral_commission_value', '0');

        return view('admin.settings.index', compact(
            'wompiPublicKey',
            'wompiIntegritySecret',
            'referralEnabled',
            'referralBonusSessions',
            'referralCommissionType',
            'referralCommissionValue'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'wompi_public_key' => 'nullable|string',
            'wompi_integrity_secret' => 'nullable|string',
            'referral_enabled' => 'nullable|in:0,1',
            'referral_bonus_sessions' => 'nullable|integer|min:1|max:50',
            'referral_commission_type' => 'nullable|in:fixed,percentage',
            'referral_commission_value' => 'nullable|numeric|min:0',
        ]);

        Setting::updateOrCreate(['key' => 'wompi_public_key'], ['value' => $request->wompi_public_key]);
        Setting::updateOrCreate(['key' => 'wompi_integrity_secret'], ['value' => $request->wompi_integrity_secret]);

        // Guardar configuración de referidos
        Setting::updateOrCreate(['key' => 'referral_enabled'], ['value' => $request->has('referral_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'referral_bonus_sessions'], ['value' => $request->referral_bonus_sessions ?? '3']);
        Setting::updateOrCreate(['key' => 'referral_commission_type'], ['value' => $request->referral_commission_type ?? 'fixed']);
        Setting::updateOrCreate(['key' => 'referral_commission_value'], ['value' => $request->referral_commission_value ?? '0']);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}

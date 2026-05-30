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
        $staffCommissionTarget = Setting::get('staff_commission_target', '0');

        // Configuración de Disparos
        $shotsPerZone = Setting::get('shots_per_zone', '600');
        $shotsPerMinizone = Setting::get('shots_per_minizone', '200');

        // Configuración de Agrandamientos
        $upgradeCommissionType = Setting::get('upgrade_commission_type', 'fixed');
        $upgradeCommissionValue = Setting::get('upgrade_commission_value', '0');
        $upgradeCommissionTarget = Setting::get('upgrade_commission_target', '0');

        // Configuración de abonos
        $minimumAbonoAmount = Setting::get('minimum_abono_amount', '50000');

        return view('admin.settings.index', compact(
            'wompiPublicKey',
            'wompiIntegritySecret',
            'referralEnabled',
            'referralBonusSessions',
            'referralCommissionType',
            'referralCommissionValue',
            'staffCommissionTarget',
            'shotsPerZone',
            'shotsPerMinizone',
            'upgradeCommissionType',
            'upgradeCommissionValue',
            'upgradeCommissionTarget',
            'minimumAbonoAmount'
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
            'staff_commission_target' => 'nullable|numeric|min:0',
            'shots_per_zone' => 'nullable|integer|min:1',
            'shots_per_minizone' => 'nullable|integer|min:1',
            'upgrade_commission_type' => 'nullable|in:fixed,percentage',
            'upgrade_commission_value' => 'nullable|numeric|min:0',
            'upgrade_commission_target' => 'nullable|numeric|min:0',
            'minimum_abono_amount' => 'nullable|integer|min:0',
        ]);

        Setting::updateOrCreate(['key' => 'wompi_public_key'], ['value' => $request->wompi_public_key]);
        Setting::updateOrCreate(['key' => 'wompi_integrity_secret'], ['value' => $request->wompi_integrity_secret]);

        // Guardar configuración de referidos
        Setting::updateOrCreate(['key' => 'referral_enabled'], ['value' => $request->has('referral_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'referral_bonus_sessions'], ['value' => $request->referral_bonus_sessions ?? '3']);
        Setting::updateOrCreate(['key' => 'referral_commission_type'], ['value' => $request->referral_commission_type ?? 'fixed']);
        Setting::updateOrCreate(['key' => 'referral_commission_value'], ['value' => $request->referral_commission_value ?? '0']);
        Setting::updateOrCreate(['key' => 'staff_commission_target'], ['value' => $request->staff_commission_target ?? '0']);

        // Guardar configuración de disparos
        Setting::updateOrCreate(['key' => 'shots_per_zone'], ['value' => $request->shots_per_zone ?? '600']);
        Setting::updateOrCreate(['key' => 'shots_per_minizone'], ['value' => $request->shots_per_minizone ?? '200']);

        // Guardar configuración de comisión de agrandamiento
        Setting::updateOrCreate(['key' => 'upgrade_commission_type'], ['value' => $request->upgrade_commission_type ?? 'fixed']);
        Setting::updateOrCreate(['key' => 'upgrade_commission_value'], ['value' => $request->upgrade_commission_value ?? '0']);
        Setting::updateOrCreate(['key' => 'upgrade_commission_target'], ['value' => $request->upgrade_commission_target ?? '0']);

        // Guardar configuración de abonos
        Setting::updateOrCreate(['key' => 'minimum_abono_amount'], ['value' => $request->minimum_abono_amount ?? '50000']);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}

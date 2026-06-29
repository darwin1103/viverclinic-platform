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
        $referralSalesEnabled = Setting::get('referral_sales_enabled', '1');

        // Configuración de Disparos
        $shotsPerZone = Setting::get('shots_per_zone', '600');
        $shotsPerMinizone = Setting::get('shots_per_minizone', '200');

        // Configuración de Agrandamientos
        $upgradeSalesEnabled = Setting::get('upgrade_sales_enabled', '1');

        // Configuración de Recompras
        $repurchaseSalesEnabled = Setting::get('repurchase_sales_enabled', '1');

        // Meta global de comisiones (unificada)
        $commissionTarget = Setting::get('commission_target', '0');

        // Configuración de abonos
        $minimumAbonoAmount = Setting::get('minimum_abono_amount', '50000');

        return view('admin.settings.index', compact(
            'wompiPublicKey',
            'wompiIntegritySecret',
            'referralEnabled',
            'referralBonusSessions',
            'referralSalesEnabled',
            'shotsPerZone',
            'shotsPerMinizone',
            'upgradeSalesEnabled',
            'repurchaseSalesEnabled',
            'commissionTarget',
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
            'referral_sales_enabled' => 'nullable|in:0,1',
            'shots_per_zone' => 'nullable|integer|min:1',
            'shots_per_minizone' => 'nullable|integer|min:1',
            'upgrade_sales_enabled' => 'nullable|in:0,1',
            'repurchase_sales_enabled' => 'nullable|in:0,1',
            'commission_target' => 'nullable|numeric|min:0',
            'minimum_abono_amount' => 'nullable|integer|min:0',
        ]);

        Setting::updateOrCreate(['key' => 'wompi_public_key'], ['value' => $request->wompi_public_key]);
        Setting::updateOrCreate(['key' => 'wompi_integrity_secret'], ['value' => $request->wompi_integrity_secret]);

        // Guardar configuración de referidos
        Setting::updateOrCreate(['key' => 'referral_enabled'], ['value' => $request->has('referral_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'referral_bonus_sessions'], ['value' => $request->referral_bonus_sessions ?? '3']);
        Setting::updateOrCreate(['key' => 'referral_sales_enabled'], ['value' => $request->has('referral_sales_enabled') ? '1' : '0']);

        // Guardar configuración de disparos
        Setting::updateOrCreate(['key' => 'shots_per_zone'], ['value' => $request->shots_per_zone ?? '600']);
        Setting::updateOrCreate(['key' => 'shots_per_minizone'], ['value' => $request->shots_per_minizone ?? '200']);

        // Guardar configuración de ventas
        Setting::updateOrCreate(['key' => 'upgrade_sales_enabled'], ['value' => $request->has('upgrade_sales_enabled') ? '1' : '0']);
        Setting::updateOrCreate(['key' => 'repurchase_sales_enabled'], ['value' => $request->has('repurchase_sales_enabled') ? '1' : '0']);

        // Guardar meta global de ventas/comisiones
        Setting::updateOrCreate(['key' => 'commission_target'], ['value' => $request->commission_target ?? '0']);

        // Guardar configuración de abonos
        Setting::updateOrCreate(['key' => 'minimum_abono_amount'], ['value' => $request->minimum_abono_amount ?? '50000']);

        return back()->with('success', 'Configuración actualizada correctamente.');
    }
}

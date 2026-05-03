<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\Setting;
use Illuminate\Support\Facades\Auth;

class ReferralsController extends Controller
{
    /**
     * Display the referrals dashboard for the patient.
     */
    public function index()
    {
        $user = Auth::user();

        // Asegurar que el usuario tenga un código de referido
        if (!$user->referral_code) {
            $user->referral_code = \App\Models\User::generateReferralCode();
            $user->save();
        }

        $referralLink = $user->getReferralLink();

        // Referidos hechos por este usuario
        $referrals = Referral::where('referrer_id', $user->id)
            ->with('referred')
            ->latest()
            ->get();

        // Estadísticas
        $totalReferred = $referrals->count();
        $successfulReferrals = $referrals->where('status', 'rewarded')->count();
        $totalBonusSessions = $referrals->where('status', 'rewarded')->sum('bonus_sessions');
        $pendingReferrals = $referrals->where('status', 'registered')->count();

        // Configuración actual
        $bonusSessionsConfig = Setting::get('referral_bonus_sessions', 3);
        $referralEnabled = Setting::get('referral_enabled', '1');
        $referralCode = $user->referral_code;

        // Active treatments to apply the sessions
        $activeTreatments = \App\Models\ContractedTreatment::with('treatment')
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->get();

        return view('referrals.index', compact(
            'user',
            'referralCode',
            'referralLink',
            'referrals',
            'totalReferred',
            'successfulReferrals',
            'totalBonusSessions',
            'pendingReferrals',
            'bonusSessionsConfig',
            'referralEnabled',
            'activeTreatments'
        ));
    }

    /**
     * Redeem bonus sessions from a referral to an active treatment.
     */
    public function redeem(\Illuminate\Http\Request $request, Referral $referral)
    {
        $request->validate([
            'contracted_treatment_id' => 'required|exists:contracted_treatments,id',
        ]);

        $user = Auth::user();

        // Verify referral belongs to user, is rewarded and not redeemed
        if ($referral->referrer_id !== $user->id || $referral->status !== 'rewarded' || $referral->sessions_redeemed) {
            return back()->with('error', 'Este referido no está disponible para redimir.');
        }

        // Verify treatment belongs to user and is active
        $treatment = \App\Models\ContractedTreatment::where('id', $request->contracted_treatment_id)
            ->where('user_id', $user->id)
            ->whereNotIn('status', ['Cancelled', 'Completed'])
            ->first();

        if (!$treatment) {
            return back()->with('error', 'El tratamiento seleccionado no es válido o no está activo.');
        }

        // Apply sessions
        $treatment->increment('sessions', $referral->bonus_sessions);
        
        // Mark as redeemed
        $referral->update([
            'sessions_redeemed' => true,
        ]);

        return back()->with('success', "¡Excelente! Se han sumado {$referral->bonus_sessions} sesiones a tu tratamiento.");
    }
}

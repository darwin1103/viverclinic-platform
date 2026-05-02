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

        return view('referrals.index', compact(
            'user',
            'referralLink',
            'referrals',
            'totalReferred',
            'successfulReferrals',
            'totalBonusSessions',
            'pendingReferrals',
            'bonusSessionsConfig',
            'referralEnabled'
        ));
    }
}

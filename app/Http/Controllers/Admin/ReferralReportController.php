<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\ContractedTreatment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReferralReportController extends Controller
{
    /**
     * Display a listing of the referrals.
     */
    public function index(Request $request): View
    {
        $referrers = User::with('referrals')
            ->has('referrals')
            ->get()
            ->map(function ($referrer) {
                $economicImpact = 0;
                
                foreach ($referrer->referrals as $referral) {
                    $economicImpact += ContractedTreatment::where('user_id', $referral->id)
                        ->sum('total_price');
                }

                $referrer->economic_impact = $economicImpact;
                $referrer->referrals_count = $referrer->referrals->count();

                return $referrer;
            })
            ->sortByDesc('economic_impact');

        return view('admin.referrals.report', compact('referrers'));
    }
}

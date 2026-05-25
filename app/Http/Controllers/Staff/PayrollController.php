<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PayrollSettlement;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\PackageUpgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $profile = $user->staffProfile;

        if (!$profile) {
            abort(403, 'No tienes perfil de staff configurado.');
        }

        $month = $request->get('month', now()->format('m'));
        $year = $request->get('year', now()->format('Y'));

        // Historial de liquidaciones pagadas o registradas
        $settlements = PayrollSettlement::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        // Comisiones actuales del mes (aún no liquidadas o ya liquidadas, para mostrar progreso)
        $currentReferralCommissions = Referral::where('staff_id', $user->id)
            ->where('status', 'rewarded')
            ->whereMonth('rewarded_at', $month)
            ->whereYear('rewarded_at', $year)
            ->sum('staff_commission');

        $currentUpgradeCommissions = PackageUpgrade::where('staff_user_id', $user->id)
            ->where('payment_status', 'APPROVED')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('commission_amount');

        $referralTarget = (float) Setting::get('staff_commission_target', 0);
        $upgradeTarget = (float) Setting::get('upgrade_commission_target', 0);

        $referralProgress = 0;
        if ($referralTarget > 0) {
            $referralProgress = min(100, ($currentReferralCommissions / $referralTarget) * 100);
        }

        $upgradeProgress = 0;
        if ($upgradeTarget > 0) {
            $upgradeProgress = min(100, ($currentUpgradeCommissions / $upgradeTarget) * 100);
        }

        return view('staff.payroll.index', compact(
            'profile', 'month', 'year', 'settlements',
            'currentReferralCommissions', 'referralTarget', 'referralProgress',
            'currentUpgradeCommissions', 'upgradeTarget', 'upgradeProgress'
        ));
    }
}

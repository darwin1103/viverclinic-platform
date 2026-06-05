<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PayrollSettlement;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\PackageUpgrade;
use App\Models\RepurchaseCommission;
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

        $currentRepurchaseCommissions = RepurchaseCommission::where('staff_user_id', $user->id)
            ->where('status', 'approved')
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->sum('commission_amount');

        // Unified commission target
        $commissionTarget = (float) Setting::get('commission_target', 0);
        $totalCommissions = $currentReferralCommissions + $currentUpgradeCommissions + $currentRepurchaseCommissions;

        $commissionProgress = 0;
        if ($commissionTarget > 0) {
            $commissionProgress = min(100, ($totalCommissions / $commissionTarget) * 100);
        }

        return view('staff.payroll.index', compact(
            'profile', 'month', 'year', 'settlements',
            'currentReferralCommissions', 'currentUpgradeCommissions', 'currentRepurchaseCommissions',
            'totalCommissions', 'commissionTarget', 'commissionProgress'
        ));
    }
}

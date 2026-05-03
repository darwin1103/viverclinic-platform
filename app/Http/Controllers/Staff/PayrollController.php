<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PayrollSettlement;
use App\Models\Referral;
use App\Models\Setting;
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
        $currentCommissions = Referral::where('staff_id', $user->id)
            ->where('status', 'rewarded')
            ->whereMonth('rewarded_at', $month)
            ->whereYear('rewarded_at', $year)
            ->sum('staff_commission');

        $commissionTarget = (float) Setting::get('staff_commission_target', 0);
        $progressPercentage = 0;
        
        if ($commissionTarget > 0) {
            $progressPercentage = min(100, ($currentCommissions / $commissionTarget) * 100);
        }

        return view('staff.payroll.index', compact(
            'profile', 'month', 'year', 'settlements', 'currentCommissions', 'commissionTarget', 'progressPercentage'
        ));
    }
}

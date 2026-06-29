<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\PayrollSettlement;
use App\Models\Sale;
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

        // Ventas actuales del mes
        $sales = Sale::where('staff_user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $currentSalesCount = $sales->count();
        $currentSalesTotal = $sales->sum('first_payment_amount');

        // Sales target (instead of commission target)
        $salesTarget = (float) Setting::get('commission_target', 0); // Keep using the same setting, but interpreted as sales volume goal

        $salesProgress = 0;
        if ($salesTarget > 0) {
            $salesProgress = min(100, ($currentSalesTotal / $salesTarget) * 100);
        }

        return view('staff.payroll.index', compact(
            'profile', 'month', 'year', 'settlements',
            'currentSalesCount', 'currentSalesTotal',
            'salesTarget', 'salesProgress'
        ));
    }
}

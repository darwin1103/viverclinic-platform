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

        // 1. VENTAS INDIVIDUALES (Del Empleado Actual)
        $sales = Sale::where('staff_user_id', $user->id)
            ->whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();

        $currentSalesCount = $sales->count();
        $currentSalesTotal = $sales->sum('first_payment_amount');

        // 2. VENTAS GLOBALES (De Todo el Equipo)
        $globalSales = Sale::whereMonth('created_at', $month)
            ->whereYear('created_at', $year)
            ->get();
            
        $globalSalesCount = $globalSales->count();
        $globalSalesTotal = $globalSales->sum('first_payment_amount');

        // 3. METAS
        // Meta mensual en Monto (Dinero)
        $salesTargetAmount = (float) Setting::get('commission_target', 0);
        
        // Meta mensual en Cantidad (Número de Ventas)
        $salesTargetCount = (int) Setting::get('commission_target_count', 0);

        // 4. PROGRESOS GLOBALES (Equipo)
        $globalAmountProgress = 0;
        if ($salesTargetAmount > 0) {
            $globalAmountProgress = min(100, ($globalSalesTotal / $salesTargetAmount) * 100);
        }
        
        $globalCountProgress = 0;
        if ($salesTargetCount > 0) {
            $globalCountProgress = min(100, ($globalSalesCount / $salesTargetCount) * 100);
        }

        return view('staff.payroll.index', compact(
            'profile', 'month', 'year', 'settlements',
            'currentSalesCount', 'currentSalesTotal',
            'globalSalesCount', 'globalSalesTotal',
            'salesTargetAmount', 'salesTargetCount',
            'globalAmountProgress', 'globalCountProgress'
        ));
    }
}

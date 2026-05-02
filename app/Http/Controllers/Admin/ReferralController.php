<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    /**
     * Mostrar el panel de gestión de referidos.
     */
    public function index(Request $request)
    {
        $query = Referral::with(['referrer', 'referred', 'staff'])->latest();

        // Filtro por estado
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filtro por búsqueda (nombre del referidor o referido)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('referrer', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('referred', function ($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                });
            });
        }

        $referrals = $query->paginate(20)->withQueryString();

        // Estadísticas
        $totalReferrals = Referral::count();
        $rewardedCount = Referral::where('status', 'rewarded')->count();
        $totalBonusSessions = Referral::where('status', 'rewarded')->sum('bonus_sessions');
        $pendingCommissions = Referral::where('staff_commission_status', 'pending')->sum('staff_commission');
        $registeredCount = Referral::where('status', 'registered')->count();

        return view('admin.referrals.index', compact(
            'referrals',
            'totalReferrals',
            'rewardedCount',
            'totalBonusSessions',
            'pendingCommissions',
            'registeredCount'
        ));
    }

    /**
     * Marcar comisión como pagada.
     */
    public function markCommissionPaid(Referral $referral)
    {
        if ($referral->staff_commission_status !== 'pending') {
            return back()->with('error', 'Esta comisión no está pendiente.');
        }

        $referral->update([
            'staff_commission_status' => 'paid',
        ]);

        return back()->with('success', 'Comisión marcada como pagada.');
    }
}

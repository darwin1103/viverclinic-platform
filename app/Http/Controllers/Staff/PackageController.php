<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\ContractedTreatment;

class PackageController extends Controller
{
    /**
     * Display a listing of the packages.
     */
    public function index(): View
    {
        $branchId = auth()->user()->staffProfile->branch_id ?? null;
        
        $packages = ContractedTreatment::with(['user', 'treatment'])
            ->where('branch_id', $branchId)
            ->latest()
            ->get();
            
        return view('staff.packages.index', compact('packages'));
    }
}

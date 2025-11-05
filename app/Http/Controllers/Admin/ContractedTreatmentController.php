<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\BranchTreatment;
use App\Models\ContractedTreatment;
use App\Models\Treatment;
use Illuminate\Http\Request;

class ContractedTreatmentController extends Controller
{
    public function index(Request $request)
    {
        $query = ContractedTreatment::with(['user', 'branch', 'treatment'])
                    ->latest(); // Ordenar por más reciente

        // Filter by search term (client name or email)
        if ($request->filled('search')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        // Filter by branch
        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        // Filter by treatment
        if ($request->filled('treatment_id')) {
            $query->where('treatment_id', $request->treatment_id);
        }

        $contractedTreatments = $query->paginate(15)->withQueryString();

        // Data for filters
        $treatments = Treatment::where('active', true)->orderBy('name')->get();

        $branches = Branch::all();

        $selectedBranchID = $request->input('branch_id') ?? '';

        return view('admin.contracted-treatment.index', compact(
            'contractedTreatments',
            'treatments',
            'branches',
            'selectedBranchID'
        ));

    }

    public function show(ContractedTreatment $contractedTreatment)
    {
        // Eager load the relationships to prevent N+1 query issues in the view.
        // This ensures we fetch the user, branch, and treatment data in one go.
        $contractedTreatment->load(['user', 'branch', 'treatment']);

        // Pass the fully loaded model to the view.
        return view('admin.contracted-treatment.show', compact('contractedTreatment'));
    }

}

<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\ContractedTreatment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ContractedTreatmentController extends Controller
{

    public function index(Request $request)
    {

        $user = Auth::user();

        $query = ContractedTreatment::with(['user', 'treatment'])
            ->where('user_id', $user->id)
            ->latest(); // Ordenar por más reciente

        $contractedTreatments = $query->paginate(15);

        return view('client.contracted-treatment.index', compact('contractedTreatments'));
    }

    public function show(ContractedTreatment $contractedTreatment)
    {

        $contractedTreatment->load(['user', 'branch', 'treatment']);
        return view('client.contracted-treatment.show', compact('contractedTreatment'));

    }


}

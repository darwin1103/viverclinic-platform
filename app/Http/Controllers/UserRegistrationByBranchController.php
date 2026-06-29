<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class UserRegistrationByBranchController extends Controller
{

    /**
     * Show the form for creating a new resource.
     */
    public function __invoke(Branch $branch)
    {
        $data = [
            'branchId' => $branch->id,
            'referralCode' => request()->query('ref'),
            'documentTypes' => \App\Models\DocumentType::where('status', true)->get(),
            'genres' => \App\Models\Gender::where('status', true)->get(),
        ];

        return view('registration-by-branch.create', $data);
    }

}

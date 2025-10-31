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
        ];

        return view('registration-by-branch.create', $data);
    }

}

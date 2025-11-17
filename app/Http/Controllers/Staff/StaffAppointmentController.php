<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAppointmentController extends Controller
{

    /**
     * Display the appointment management page
     */
    public function index()
    {

        $user = Auth::user();

        // Get current user's branch
        $currentBranch = $user->staffProfile->branch->id;

        $data = [

        ];

        return view('staff.appointments.index', $data);
    }

}

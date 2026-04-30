<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\Referral;
use App\Models\User;

class ReferralController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $referrals = Referral::with('referrer')->latest()->get();
        return view('admin.referrals.index', compact('referrals'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $patients = User::role('PATIENT')->select(['id', 'name'])->get();
        return view('admin.referrals.create', compact('patients'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'referrer_id' => 'required|exists:users,id',
            'referred_name' => 'required|string|max:255',
            'referred_email' => 'nullable|email|max:255',
            'referred_phone' => 'nullable|string|max:20',
        ]);

        Referral::create($validated);

        return redirect()->route('admin.referrals.index')->with('success', 'Referido registrado exitosamente.');
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class VirtualWalletController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $wallet = $user->virtualWallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00]
        );
        $transactions = $wallet->transactions()->latest()->get();

        return view('virtual-wallet.index', compact('wallet', 'transactions'));
    }

    /**
     * Add balance to a user's wallet manually (Admin).
     */
    public function addBalance(Request $request, $userId)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
        ]);

        $user = \App\Models\User::findOrFail($userId);
        
        $wallet = $user->virtualWallet()->firstOrCreate(
            ['user_id' => $user->id],
            ['balance' => 0.00]
        );

        $wallet->balance += $validated['amount'];
        $wallet->save();

        $wallet->transactions()->create([
            'amount' => $validated['amount'],
            'type' => 'ingreso',
            'description' => $validated['description'] ?? 'Recarga manual de administrador',
        ]);

        return back()->with('success', 'Saldo agregado exitosamente a la billetera.');
    }
}

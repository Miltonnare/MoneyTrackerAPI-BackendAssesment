<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Store a newly created wallet in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'name' => 'required'
        ]);

        $wallet = Wallet::create($request->only('user_id', 'name'));

        return response()->json($wallet, 201);
    }

    /**
     * Display the specified wallet with transactions and balance.
     */
    public function show($id)
    {
        $wallet = Wallet::with('transactions')->findOrFail($id);

        return response()->json([
            'wallet' => $wallet,
            'balance' => $wallet->balance,
            'transactions' => $wallet->transactions
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'wallet_id' => 'required|exists:wallets,id',
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01'
        ]);

        $transaction = Transaction::create($request->all());

        return response()->json($transaction, 201);
    }
}

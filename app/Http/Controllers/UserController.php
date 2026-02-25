<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email'
        ]);

        $user = User::create($request->only('name', 'email'));

        return response()->json($user, 201);
    }

    /**
     * Display the specified user with wallets and total balance.
     */
    public function show($id)
    {
        $user = User::with('wallets.transactions')->findOrFail($id);

        $wallets = $user->wallets->map(function ($wallet) {
            return [
                'id' => $wallet->id,
                'name' => $wallet->name,
                'balance' => $wallet->balance
            ];
        });

        $totalBalance = $wallets->sum('balance');

        return response()->json([
            'user' => $user->only(['id','name','email']),
            'wallets' => $wallets,
            'total_balance' => $totalBalance
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class WalletController extends Controller
{
    /**
     * Store a newly created wallet in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'name' => 'required|string|max:255'
            ]);

            $wallet = Wallet::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Wallet created successfully',
                'data' => $wallet
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => [
                    'errors' => $e->errors()
                ]
            ], 422);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create wallet',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }

    /**
     * Display the specified wallet with transactions and balance.
     */
    public function show($id)
    {
        try {
            $wallet = Wallet::with('transactions')->findOrFail($id);

            return response()->json([
                'success' => true,
                'message' => 'Wallet retrieved successfully',
                'data' => [
                    'wallet' => [
                        'id' => $wallet->id,
                        'user_id' => $wallet->user_id,
                        'name' => $wallet->name,
                        'created_at' => $wallet->created_at,
                        'updated_at' => $wallet->updated_at
                    ],
                    'balance' => $wallet->balance,
                    'transactions' => $wallet->transactions
                ]
            ], 200);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found',
                'data' => null
            ], 404);

        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve wallet',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }
}

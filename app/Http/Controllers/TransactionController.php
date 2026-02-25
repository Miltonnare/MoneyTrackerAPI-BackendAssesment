<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Exception;

class TransactionController extends Controller
{
    /**
     * Store a newly created transaction in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'wallet_id' => 'required|exists:wallets,id',
                'type' => 'required|in:income,expense',
                'amount' => 'required|numeric|min:0.01'
            ]);

            $transaction = Transaction::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction
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
                'message' => 'Failed to create transaction',
                'data' => [
                    'error' => $e->getMessage()
                ]
            ], 500);
        }
    }
}

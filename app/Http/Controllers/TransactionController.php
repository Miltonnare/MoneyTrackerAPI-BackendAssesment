<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Wallet;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
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

            // Begin database transaction
            DB::beginTransaction();

            try {
                // Fetch wallet with transactions to check balance
                $wallet = Wallet::with('transactions')->findOrFail($validated['wallet_id']);

                // If expense, check for insufficient funds
                if ($validated['type'] === 'expense') {
                    $currentBalance = $wallet->balance;

                    if ($validated['amount'] > $currentBalance) {
                        DB::rollBack();
                        return response()->json([
                            'success' => false,
                            'message' => 'Insufficient funds',
                            'data' => [
                                'current_balance' => $currentBalance,
                                'requested_amount' => $validated['amount']
                            ]
                        ], 422);
                    }
                }

                // Create transaction
                $transaction = Transaction::create($validated);

                // Commit transaction
                DB::commit();

                return response()->json([
                    'success' => true,
                    'message' => 'Transaction created successfully',
                    'data' => $transaction
                ], 201);

            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
                DB::rollBack();
                return response()->json([
                    'success' => false,
                    'message' => 'Wallet not found',
                    'data' => null
                ], 404);

            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }

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

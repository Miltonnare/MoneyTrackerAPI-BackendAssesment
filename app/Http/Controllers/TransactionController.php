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

            // Use database transaction with row locking
            $result = DB::transaction(function () use ($validated) {
                // Lock the wallet row to prevent race conditions
                $wallet = Wallet::where('id', $validated['wallet_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$wallet) {
                    throw new \Illuminate\Database\Eloquent\ModelNotFoundException('Wallet not found');
                }

                // Check for insufficient funds on expense transactions
                if ($validated['type'] === 'expense') {
                    if ($wallet->balance < $validated['amount']) {
                        throw new \Exception('Insufficient funds', 422);
                    }

                    // Subtract expense from wallet balance
                    $wallet->balance -= $validated['amount'];
                } else {
                    // Add income to wallet balance
                    $wallet->balance += $validated['amount'];
                }

                // Save the updated wallet balance
                $wallet->save();

                // Create the transaction
                $transaction = Transaction::create($validated);

                return [
                    'transaction' => $transaction,
                    'wallet' => $wallet
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Transaction created successfully',
                'data' => [
                    'transaction' => $result['transaction'],
                    'wallet_balance' => $result['wallet']->balance
                ]
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => [
                    'errors' => $e->errors()
                ]
            ], 422);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Wallet not found',
                'data' => null
            ], 404);

        } catch (Exception $e) {
            // Check if it's the insufficient funds exception
            if ($e->getMessage() === 'Insufficient funds' && $e->getCode() == 422) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient funds',
                    'data' => null
                ], 422);
            }

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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    /**
     * Get the user that owns the wallet.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the transactions for the wallet.
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get the wallet balance.
     * Calculated as: income transactions - expense transactions
     */
    public function getBalanceAttribute()
    {
        return $this->transactions->sum(function ($transaction) {
            return $transaction->type === 'income' 
                ? $transaction->amount 
                : -$transaction->amount;
        });
    }
}

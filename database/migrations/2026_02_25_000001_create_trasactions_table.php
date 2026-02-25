<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->foreignId('wallet_id')
                  ->constrained('wallets')
                  ->onDelete('cascade');
                  // If wallet is deleted, its transactions are deleted

            $table->enum('type', ['income', 'expense']);
            // Restricts values at database level

            $table->decimal('amount', 15, 2);
            // 15 digits total, 2 decimal places (supports large values safely)

            $table->text('description')->nullable();
            // Optional field

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
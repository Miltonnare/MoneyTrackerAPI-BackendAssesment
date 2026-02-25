<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallets', function (Blueprint $table) {
            $table->id(); // Primary key

            $table->foreignId('user_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
                  // Deletes wallets automatically if user is deleted

            $table->string('name'); // e.g., "Business A Wallet"

            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};

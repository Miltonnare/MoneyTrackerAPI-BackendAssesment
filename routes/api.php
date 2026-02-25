<?php

use App\Http\Controllers\UserController;
use App\Http\Controllers\WalletController;
use App\Http\Controllers\TransactionController;
use Illuminate\Support\Facades\Route;

// User Routes
Route::post('/users', [UserController::class, 'store']);
Route::get('/users/{id}', [UserController::class, 'show']);

// Wallet Routes
Route::post('/wallets', [WalletController::class, 'store']);
Route::get('/wallets/{id}', [WalletController::class, 'show']);

// Transaction Routes
Route::post('/transactions', [TransactionController::class, 'store']);

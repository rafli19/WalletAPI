<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\WalletController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout',  [AuthController::class, 'logout']);
    Route::get('/me',       [AuthController::class, 'me']);
    Route::put('/profile',  [AuthController::class, 'updateProfile']);
    Route::post('/profile', [AuthController::class, 'updateProfile']);

    Route::get('/wallet',       [WalletController::class, 'balance']);
    Route::get('/user/lookup',  [WalletController::class, 'lookup']);
    Route::post('/topup',       [WalletController::class, 'topup']);
    Route::post('/transfer',    [WalletController::class, 'transfer']);
    Route::get('/transactions', [WalletController::class, 'transactions']);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Endpoint tidak ditemukan.',
    ], 404);
});
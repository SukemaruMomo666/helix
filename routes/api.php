<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\KeranjangController;
use App\Http\Controllers\TransactionController;
// use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ChatController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [AuthController::class, 'loginMobile']);
Route::post('/register', [AuthController::class, 'registerMobile']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', function (Request $request) {
        return response()->json([
            'status' => 'success',
            'user' => $request->user()
        ]);
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Berhasil logout dari aplikasi mobile.'
        ], 200);
    });

    // We keep the rest commented or clean it up if needed.
    // The previous error was caused by blind search/replace.
});
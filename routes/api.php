<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/hello', function () {
    return response()->json(['message' => 'Hello, World!']);
});

Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'registration']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/forgot-password', [App\Http\Controllers\Api\ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [App\Http\Controllers\Api\ForgotPasswordController::class, 'reset']);
    Route::post('2fa/{user_id}', [App\Http\Controllers\Api\TwoFAController::class, 'store']);
    Route::get('2fa/resend/{user_id}', [App\Http\Controllers\Api\TwoFAController::class, 'resend']);
});

Route::prefix('auth/admin')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'registration']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
});

Route::prefix('auth')->middleware('auth:sanctum')->group(function () {

    Route::post('/update-profile', [App\Http\Controllers\Api\ProfileController::class, 'updateProfile']);
    Route::post('/change-password', [App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
    Route::post('/enable-two-factor', [App\Http\Controllers\Api\TwoFAController::class, 'enableTwoFactorAuth']);
    Route::post('/disable-two-factor', [App\Http\Controllers\Api\TwoFAController::class, 'disableTwoFactorAuth']);
});

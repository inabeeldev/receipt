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

Route::post('/admin-send-user', [App\Http\Controllers\Api\Admin\ChatController::class, 'adminMessage'])->middleware('admin_auth');
Route::post('/user-send-admin', [App\Http\Controllers\Api\Admin\ChatController::class, 'userMessage'])->middleware('auth:sanctum');
Route::get('/admin-messages', [App\Http\Controllers\Api\Admin\ChatController::class, 'getAdminMessages'])->middleware('admin_auth');
Route::get('/user-messages', [App\Http\Controllers\Api\Admin\ChatController::class, 'getUserMessages'])->middleware('auth:sanctum');
Route::get('/get-chat-users', [App\Http\Controllers\Api\Admin\ChatController::class, 'getUsersInAdminMessages'])->middleware('admin_auth');
Route::get('/user-messages/{userId}', [App\Http\Controllers\Api\Admin\ChatController::class, 'adminUserMessages'])->middleware('admin_auth');
Route::get('/user-messages-to-admin', [App\Http\Controllers\Api\Admin\ChatController::class, 'userAdminMessages'])->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'registration']);
    Route::post('/login', [App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/forgot-password', [App\Http\Controllers\Api\ForgotPasswordController::class, 'sendResetLinkEmail']);
    Route::post('/reset-password', [App\Http\Controllers\Api\ForgotPasswordController::class, 'reset']);
    Route::post('2fa/{user_id}', [App\Http\Controllers\Api\TwoFAController::class, 'store']);
    Route::get('2fa/resend/{user_id}', [App\Http\Controllers\Api\TwoFAController::class, 'resend']);
});

Route::prefix('auth/admin')->group(function () {
    Route::post('/register', [App\Http\Controllers\Api\Admin\AuthController::class, 'adminRegistration']);
    Route::post('/login', [App\Http\Controllers\Api\Admin\AuthController::class, 'login']);
});

Route::prefix('admin')->middleware('admin_auth')->group(function () {
    Route::put('/profile/edit', [App\Http\Controllers\Api\Admin\AuthController::class, 'editAdminProfile']);
    Route::delete('/delete', [App\Http\Controllers\Api\Admin\AuthController::class, 'deleteAdmin']);

    Route::prefix('product')->group(function () {
        Route::get('/list', [App\Http\Controllers\Api\Admin\ProductController::class, 'list']);
        Route::get('/show/{id}', [App\Http\Controllers\Api\Admin\ProductController::class, 'show']);
        Route::post('/store', [App\Http\Controllers\Api\Admin\ProductController::class, 'store']);
        Route::put('/update/{id}', [App\Http\Controllers\Api\Admin\ProductController::class, 'update']);
        Route::delete('/delete/{id}', [App\Http\Controllers\Api\Admin\ProductController::class, 'destroy']);
    });

    Route::prefix('company')->group(function () {
        Route::post('/register', [App\Http\Controllers\Api\Admin\CompanyController::class, 'registration']);
        Route::get('/list', [App\Http\Controllers\Api\Admin\CompanyController::class, 'list']);
        Route::post('/update-profile/{userId}', [App\Http\Controllers\Api\Admin\CompanyController::class, 'updateProfile']);
        Route::post('/change-password/{userId}', [App\Http\Controllers\Api\Admin\CompanyController::class, 'changePassword']);
        Route::delete('/delete-company/{userId}', [App\Http\Controllers\Api\Admin\CompanyController::class, 'destroy']);
    });

});

Route::prefix('auth')->middleware('auth:sanctum')->group(function () {
    Route::post('/update-profile', [App\Http\Controllers\Api\ProfileController::class, 'updateProfile']);
    Route::post('/change-password', [App\Http\Controllers\Api\ProfileController::class, 'changePassword']);
    Route::post('/enable-two-factor', [App\Http\Controllers\Api\TwoFAController::class, 'enableTwoFactorAuth']);
    Route::post('/disable-two-factor', [App\Http\Controllers\Api\TwoFAController::class, 'disableTwoFactorAuth']);

});

Route::prefix('receipt')->middleware('auth:sanctum')->group(function () {
    Route::get('/show-products', [App\Http\Controllers\Api\ReceiptController::class, 'showProducts']);
    Route::post('/generate-receipt', [App\Http\Controllers\Api\ReceiptController::class, 'generateReceipt']);
    Route::get('/show-receipts', [App\Http\Controllers\Api\ReceiptController::class, 'showReceipts']);
    Route::get('/get-receipt/{receiptId}', [App\Http\Controllers\Api\ReceiptController::class, 'getReceipt']);
    Route::get('/get-invoice/{receiptId}', [App\Http\Controllers\Api\ReceiptController::class, 'getInvoice']);
    Route::get('/send-receipt/{receiptId}', [App\Http\Controllers\Api\ReceiptController::class, 'sendReceipt']);
    Route::get('/deposit-transactions', [App\Http\Controllers\Api\ReceiptController::class, 'depositTransaction']);
    Route::get('/withdraw-transactions', [App\Http\Controllers\Api\ReceiptController::class, 'withdrawTransaction']);

});

Route::prefix('company')->middleware('auth:sanctum')->group(function () {
    Route::post('/chat-groups', [App\Http\Controllers\Api\Chat\ChatGroupController::class, 'create']);
    Route::get('/user-chat-groups', [App\Http\Controllers\Api\Chat\ChatGroupController::class, 'getUserGroups']);
    Route::get('/chat-groups/{group}', [App\Http\Controllers\Api\Chat\ChatGroupController::class, 'show']);
    Route::delete('/groups/{group}/leave', [App\Http\Controllers\Api\Chat\ChatGroupController::class, 'leaveGroup']);
    Route::get('get-all-companies', [App\Http\Controllers\Api\Chat\ChatInvitationController::class, 'getUsers']);
    Route::post('/chat-groups/{group}/invitations', [App\Http\Controllers\Api\Chat\ChatInvitationController::class, 'send']);
    Route::put('/chat-invitations/{invitation}/accept', [App\Http\Controllers\Api\Chat\ChatInvitationController::class, 'accept']);
    Route::get('/chat-invitations/{invitation}', [App\Http\Controllers\Api\Chat\ChatInvitationController::class, 'show']);
    Route::post('/chat-groups/{group}/messages', [App\Http\Controllers\Api\Chat\ChatMessageController::class, 'send']);
    Route::get('/chat-groups/{group}/get_messages', [App\Http\Controllers\Api\Chat\ChatMessageController::class, 'show']);
});

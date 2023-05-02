<?php

namespace MetaFox\User\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::prefix('user')->as('user.')->group(function () {
    Route::controller(UserAdminController::class)->group(function () {
        Route::patch('feature/{id}', 'feature');
        Route::post('batch-resend-verification-email', 'batchResendVerificationEmail');
        Route::patch('resend-verification-email/{id}', 'resendVerificationEmail');
        Route::patch('verify-user/{id}', 'verifyUser');
        Route::patch('batch-verify', 'batchVerify');
        Route::patch('batch-approve', 'batchApprove');
        Route::patch('batch-move-role', 'batchMoveRole');
        Route::delete('batch-delete', 'batchDelete');
        Route::patch('approve/{id}', 'approve');
        Route::patch('deny-user/{id}', 'denyUser');
    });

    Route::prefix('ban')->as('ban.')
        ->controller(UserAdminController::class)->group(function () {
            Route::post('/', 'banUser');
            Route::delete('/{id}', 'unBanUser');
        });

    Route::prefix('batch-ban')->as('batch-ban.')
        ->controller(UserAdminController::class)
        ->group(function () {
            Route::post('/', 'batchBanUser');
            Route::delete('/', 'batchUnBanUser');
        });

    // manage members

    Route::controller(CancelReasonAdminController::class)
        ->group(function () {
            Route::get('user/cancel/reason/form/{id}', 'editForm');
            Route::get('user/cancel/reason/form', 'createForm');
        });

    Route::resource('user/cancel/reason', CancelReasonAdminController::class);
    Route::resource('user/cancel/feedback', CancelFeedbackAdminController::class);
    Route::resource('user/promotion', UserPromotionAdminController::class);

    Route::resource('/relation', UserRelationAdminController::class);
    Route::resource('user', UserAdminController::class);
    Route::resource('user-gender', GenderAdminController::class);
    Route::resource('cancel-feedback', CancelFeedbackAdminController::class);
    Route::resource('cancel-reason', CancelReasonAdminController::class);
});

Route::resource('user', UserAdminController::class);

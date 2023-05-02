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
Route::prefix('user')
    ->as('user.')
    ->group(function () {
        Route::patch('/feature/{id}', [UserAdminController::class, 'feature']);

        Route::post('/batch-resend-verification-email', [UserAdminController::class, 'batchResendVerificationEmail']);
        Route::patch('/resend-verification-email/{id}', [UserAdminController::class, 'resendVerificationEmail']);
        Route::patch('/verify-user/{id}', [UserAdminController::class, 'verifyUser']);
        Route::patch('/deny-user/{id}', [UserAdminController::class, 'denyUser']);
        Route::patch('/batch-verify', [UserAdminController::class, 'batchVerify']);
        Route::patch('/batch-approve', [UserAdminController::class, 'batchApprove']);
        Route::patch('/batch-move-role', [UserAdminController::class, 'batchMoveRole']);
        Route::delete('/batch-delete', [UserAdminController::class, 'batchDelete']);

        Route::prefix('ban')
            ->as('ban.')
            ->group(function () {
                Route::post('/', [UserAdminController::class, 'banUser']);
                Route::delete('/{id}', [UserAdminController::class, 'unBanUser']);
            });

        Route::prefix('batch-ban')
            ->as('batch-ban.')
            ->group(function () {
                Route::post('/', [UserAdminController::class, 'batchBanUser']);
                Route::delete('/', [UserAdminController::class, 'batchUnBanUser']);
            });

        Route::resource('user', UserAdminController::class);
        Route::resource('user-gender', GenderAdminController::class);
    });

Route::resource('user', UserAdminController::class);

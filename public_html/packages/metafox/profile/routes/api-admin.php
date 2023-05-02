<?php

namespace MetaFox\Profile\Http\Controllers\Api;

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
Route::prefix('profile')
    ->as('profile.')
    ->group(function () {
        Route::post('field/order', [FieldAdminController::class, 'order']);
        Route::resource('field', FieldAdminController::class);
        Route::resource('profile', ProfileAdminController::class);
        Route::resource('section', SectionAdminController::class);
        Route::resource('structure', StructureAdminController::class);
    });

<?php

namespace MetaFox\Payment\Http\Controllers\Api;

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

Route::controller(GatewayController::class)
    ->prefix('payment-gateway')
    ->middleware('auth:api')
    ->group(function () {
        Route::get('configuration', 'getConfigurations');
        Route::get('configuration-form/{driver}/{id}', 'getConfigurationForm');
        Route::put('configuration-multiple', 'updateMultipleConfigurations');
        Route::put('configuration/{id}', 'updateConfigurations');
    });

Route::resource('payment-gateway', GatewayController::class)
    ->middleware('auth:api')
    ->only('index');

Route::resource('payment-order', OrderController::class)
    ->middleware('auth:api')
    ->except(['store', 'destroy']);

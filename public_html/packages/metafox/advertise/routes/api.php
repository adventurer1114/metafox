<?php

namespace MetaFox\Advertise\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('advertise')
    ->group(function () {
        Route::prefix('invoice')
            ->controller(InvoiceController::class)
            ->group(function () {
                Route::post('payment', 'payment');
                Route::post('change', 'change');
                Route::patch('cancel/{id}', 'cancel');
            });
        Route::prefix('advertise')
            ->controller(AdvertiseController::class)
            ->group(function () {
                Route::patch('active/{id}', 'active');
                Route::get('show', 'showAdvertises');
                Route::get('report/{id}', 'getReport');
                Route::patch('total/{id}', 'updateTotal');
                Route::patch('hide/{id}', 'hide');
            });
        Route::resource('advertise', AdvertiseController::class);
        Route::resource('invoice', InvoiceController::class);
    });

<?php

namespace MetaFox\Advertise\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('advertise')
    ->as('advertise.')
    ->group(function () {
        Route::prefix('placement')
            ->controller(PlacementAdminController::class)
            ->group(function () {
                Route::post('delete', 'delete');
            });
        Route::prefix('advertise')
            ->controller(AdvertiseAdminController::class)
            ->group(function () {
                Route::patch('toggleActive/{id}', 'toggleActive');
                Route::patch('approve/{id}', 'approve');
                Route::patch('deny/{id}', 'deny');
                Route::patch('paid/{id}', 'markAsPaid');
            });
        Route::resource('placement', PlacementAdminController::class)
            ->except(['destroy']);
        Route::resource('advertise', AdvertiseAdminController::class);
        Route::resource('invoice', InvoiceAdminController::class);
    });

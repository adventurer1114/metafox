<?php

namespace MetaFox\ActivityPoint\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('activitypoint')
    ->as('activitypoint.')
    ->group(function () {
        // Routes for point packages
        Route::resource('package', PointPackageController::class);
        Route::controller(PointPackageController::class)
            ->prefix('package')
            ->as('package.')
            ->group(function () {
                Route::post('purchase/{id}', 'purchase')->name('purchase');
            });

        // Routing for gifting point
        Route::controller(PointStatisticController::class)
            ->group(function () {
                Route::post('gift/{id}', 'giftPoints')->name('gift');
            });

        // Routes for point statistic
        Route::resource('statistic', PointStatisticController::class);

        // Routes for point transaction
        Route::resource('transaction', PointTransactionController::class);
        Route::resource('package-transaction', PackageTransactionController::class);

        // Routes for point settings
        Route::resource('setting', PointSettingController::class);
    });

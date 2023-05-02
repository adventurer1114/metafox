<?php

namespace MetaFox\App\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 | --------------------------------------------------------------------------
 |  API Routes
 | --------------------------------------------------------------------------
 |  This file is booted by App\Providers\RouteServiceProvider::boot()
 |  - prefix by: api/{ver}/admincp
 |  - middlewares: 'api.version', 'api','auth.admin'
 |
 |  stub: app/Console/Commands/stubs/routes/api.stub
 */

Route::prefix('app')
    ->as('app.')
    ->group(function () {
        Route::prefix('package')
            ->controller(PackageAdminController::class)
            ->as('package.')
            ->group(function () {
                Route::get('uploaded', 'uploaded')->name('uploaded');
                Route::get('purchased', 'purchased')->name('purchased');
                Route::get('{package}/export', 'export')->name('export');
                Route::post('import', 'import');
                Route::patch('{package}/install', 'install')->name('install');
                Route::patch('{package}/uninstall', 'uninstall')->name('uninstall');
            });

        Route::prefix('upgrade')
            ->as('upgrade.')
            ->controller(UpgradeAdminController::class)
            ->group(function () {
                Route::get('{step}', 'execute')->name('execute');
                Route::post('{step}', 'execute')->name('postExecute');
            });
        Route::resource('package', PackageAdminController::class);
    });

Route::controller(StoreAdminController::class)
    ->prefix('app')
    ->group(function () {
        route::get('store/product/{id}', 'show');
        route::post('store/product/install', 'install');
        route::get('store/search/form', 'form');
        route::get('store/products/browse', 'index');
    });

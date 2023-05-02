<?php

namespace MetaFox\Storage\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(DiskAdminController::class)
    ->prefix('storage')
    ->as('storage.')
    ->group(function () {
        Route::controller(AssetAdminController::class)
            ->prefix('asset')
            ->as('asset.')
            ->group(function () {
                Route::post('{asset}/upload', 'upload')->name('upload');
            });

        Route::controller(ConfigAdminController::class)
            ->prefix('config')
            ->as('config.')
            ->group(function () {
                Route::get('{driver}/{disk}/edit', 'edit')->name('edit');
                Route::put('{driver}/{disk}', 'update')->name('update');
            });

        Route::resource('asset', AssetAdminController::class);
        Route::resource('disk', DiskAdminController::class);
        Route::resource('config', ConfigAdminController::class)->except(['edit', 'update']);
    });

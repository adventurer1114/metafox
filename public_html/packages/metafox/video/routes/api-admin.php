<?php

namespace MetaFox\Video\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('video')
    ->as('video.')
    ->group(function () {
        Route::resource('service', ServiceAdminController::class);

        Route::resource('category', CategoryAdminController::class);

        Route::controller(CategoryAdminController::class)
            ->group(function () {
                Route::post('category/default/{id}', 'default')->name('category.default');
                Route::post('category/order', 'order')->name('category.order');
            });
    });

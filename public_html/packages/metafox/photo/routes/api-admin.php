<?php

namespace MetaFox\Photo\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(CategoryAdminController::class)
    ->prefix('photo')
    ->as('photo.')
    ->group(function () {
        Route::resource('category', CategoryAdminController::class);
        Route::post('category/default/{id}', 'default')->name('category.default');
        Route::post('category/order', 'order')->name('category.order');
    });

Route::as('admin')
    ->apiResource('photo', PhotoController::class);

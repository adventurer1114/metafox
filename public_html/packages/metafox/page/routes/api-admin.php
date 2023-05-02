<?php

namespace MetaFox\Page\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(PageCategoryAdminController::class)
    ->prefix('page')
    ->as('page.')
    ->group(function () {
        Route::resource('category', PageCategoryAdminController::class);
        Route::post('category/default/{id}', 'default')->name('category.default');
        Route::post('category/order', 'order')->name('category.order');
    });
Route::controller(PageClaimAdminController::class)
    ->prefix('page')
    ->as('page.')
    ->group(function () {
        Route::resource('claim', PageClaimAdminController::class);
    });

Route::as('admin')->resource('page', PageController::class);

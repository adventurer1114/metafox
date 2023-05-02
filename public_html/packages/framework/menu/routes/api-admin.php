<?php

namespace MetaFox\Menu\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('menu')
    ->as('menu.')
    ->group(function () {
        Route::post('menu-item/order', [MenuItemAdminController::class, 'order']);
        Route::resource('item', MenuItemAdminController::class);
        Route::resource('menu', MenuAdminController::class);
    });

Route::controller(MenuAdminController::class)
    ->prefix('menu')
    ->group(function () {
        Route::get('{menuName}', 'showMenu');
    });

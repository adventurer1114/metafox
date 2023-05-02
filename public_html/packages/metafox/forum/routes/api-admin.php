<?php

use Illuminate\Support\Facades\Route;
use MetaFox\Forum\Http\Controllers\Api\ForumAdminController;

Route::as('forum.')
    ->prefix('forum')
    ->group(function () {
        Route::prefix('forum')
            ->controller(ForumAdminController::class)
            ->group(function () {
                Route::post('delete', 'deleteForum');
                Route::post('order', 'order');
            });

        Route::resource('forum', ForumAdminController::class);
    });

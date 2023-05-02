<?php

namespace MetaFox\Music\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(GenreAdminController::class)
    ->prefix('music')
    ->as('music.')
    ->group(function () {
        Route::patch('genre/{id}/default', 'toggleDefault');
        Route::resource('genre', GenreAdminController::class);
    });

//Route::as('admin')
//    ->apiResource('blog', BlogAdminController::class);

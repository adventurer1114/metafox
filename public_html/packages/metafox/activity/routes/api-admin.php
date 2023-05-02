<?php

namespace MetaFox\Activity\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('feed')
    ->as('feed.')
    ->group(function () {
        Route::resource('type', TypeAdminController::class);
    });

<?php

namespace MetaFox\Authorization\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('authorization')
    ->as('authorization.')
    ->group(function () {
        Route::resource('device', DeviceController::class);
    });

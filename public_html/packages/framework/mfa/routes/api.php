<?php

namespace MetaFox\Mfa\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::prefix('mfa')
    ->middleware('auth:api')
    ->name('mfa.')
    ->group(function () {
        Route::resource('service', ServiceController::class)->only([
            'index',
        ]);

        Route::prefix('user/service')
            ->controller(UserServiceController::class)
            ->name('user.service.')
            ->group(function () {
                Route::get('setup', 'setup')->name('setup');
                Route::post('activate', 'activate')->name('activate');
                Route::delete('deactivate', 'deactivate')->name('deactivate');
            });
    });

Route::prefix('mfa/user/auth')
    ->controller(UserAuthController::class)
    ->name('mfa.user.auth.')
    ->group(function () {
        Route::get('', 'form')->name('form');
        Route::post('', 'auth')->name('auth');
    });

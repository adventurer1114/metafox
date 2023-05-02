<?php

namespace MetaFox\Video\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 * --------------------------------------------------------------------------
 *  API Routes
 * --------------------------------------------------------------------------
 *
 *  This file will be loaded by @link \MetaFox\Platform\ModuleManager::getApiRoutes()
 */
Route::prefix('video')
    ->as('video.')
    ->group(function () {
        // extra routes for video
        Route::controller(VideoController::class)->group(function () {
            Route::patch('sponsor/{id}', 'sponsor')->name('sponsor');
            Route::patch('feature/{id}', 'feature')->name('feature');
            Route::patch('approve/{id}', 'approve')->name('approve');
            Route::patch('sponsor-in-feed/{id}', 'sponsorInFeed')->name('sponsorInFeed');
            Route::post('callback/{provider}', 'callback')->name('callback');
        });

        //Routes for category
        Route::resource('category', CategoryController::class);
    });

Route::resource('video', VideoController::class);

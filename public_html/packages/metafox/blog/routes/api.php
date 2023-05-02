<?php

namespace MetaFox\Blog\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 * --------------------------------------------------------------------------
 *  API Routes
 * --------------------------------------------------------------------------
 *
 *  This file will be loaded by @link \MetaFox\Platform\ModuleManager::getApiRoutes()
 */

Route::controller(BlogController::class)
    ->prefix('blog')
    ->group(function () {
        Route::get('form/{id}', 'formUpdate');
        Route::get('form', 'formStore');
        Route::get('search-form', 'searchForm');
        Route::patch('sponsor/{id}', 'sponsor');
        Route::patch('feature/{id}', 'feature');
        Route::patch('approve/{id}', 'approve');
        Route::patch('publish/{id}', 'publish');
        Route::patch('sponsor-in-feed/{id}', 'sponsorInFeed');
    });

Route::resource('blog', BlogController::class)->middleware('auth:api');

Route::resource('blog-category', CategoryController::class)->middleware('auth:api');

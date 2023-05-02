<?php

namespace MetaFox\Search\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 * --------------------------------------------------------------------------
 *  API Routes
 * --------------------------------------------------------------------------
 *
 *  This file will be loaded by @link \MetaFox\Platform\ModuleManager::getApiRoutes()
 *
 *  stub: app/Console/Commands/stubs/routes/api.stub
 */

Route::resource('search', SearchController::class);
Route::prefix('search')
    ->as('search.')
    ->controller(SearchController::class)
    ->group(function () {
        Route::get('group', 'group')->name('group.index');
        Route::get('suggestion', 'suggestion')->name('suggest');
    });

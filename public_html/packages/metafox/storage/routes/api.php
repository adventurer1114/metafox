<?php

namespace MetaFox\Storage\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

/*
 * --------------------------------------------------------------------------
 *  API Routes
 * --------------------------------------------------------------------------
 *
 *  This file will be loaded by @link \MetaFox\Platform\ModuleManager::getApiRoutes()
 */

Route::group([
    'namespace'  => __NAMESPACE__,
    'middleware' => 'auth:api',
], function () {
});

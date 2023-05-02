<?php

namespace MetaFox\SEO\Http\Controllers\Api;

use Illuminate\Support\Facades\Route;

Route::controller(MetaController::class)
    ->prefix('seo')
    ->group(function () {
        Route::get('meta/{metaName}', 'showMetaName');
        Route::get('meta', 'showMeta');
        Route::post('meta', 'showMeta');
    });

<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance as Middleware;

class PreventRequestsDuringMaintenance extends Middleware
{
    /**
     * The URIs that should be reachable while maintenance mode is enabled.
     *
     * @var array<int, string>
     */
    protected $except = [
        'api/v1/admincp/*',
        'api/v1/core/*',
        'api/v1/menu/*',
        'api/v1/me',
        'api/v1/seo/meta/*',
        'api/v1/core/admin/settings/*',
        'api/v1/core/web/settings/*',
        'api/v1/core/translation/web/*',
        'api/v1/seo/meta',
    ];
}

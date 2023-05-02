<?php

namespace MetaFox\SEO\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

class SharingController extends Controller
{
    public function fallback($url, Request $request)
    {
        // prevent recursive request.
        $retry  = defined('MFOX_SHARING_RETRY');
        $result = $retry ? null : app('events')
            ->dispatch('parseRoute', [$url], true);

        if ($result) {
            // prevent recursive request.
            defined('MFOX_SHARING_RETRY') or define('MFOX_SHARING_RETRY', true);

            $path = sprintf('sharing' . $result['path']);

            $request = Request::create($path, 'GET', []);

            return Route::dispatch($request);
        }

        return seo_sharing_view($url);
    }
}

<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Platform\Middleware;

use Closure;
use Illuminate\Http\Request;
use MetaFox\Platform\Facades\ResourceGate;

class ApiVersion
{
    /**
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($request->has('access_token')) {
            $request->headers->set('Authorization', 'Bearer ' . $request->get('access_token'));
        }

        /* @link \MetaFox\Platform\ApiResourceManager::setVersion */
        ResourceGate::setVersion($request->route('ver'));
        $request->route()->forgetParameter('ver');

        return $next($request);
    }
}

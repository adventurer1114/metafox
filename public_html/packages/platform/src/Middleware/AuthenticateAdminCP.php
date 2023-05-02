<?php

namespace MetaFox\Platform\Middleware;

use App\Http\Middleware\Authenticate as Middleware;
use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use MetaFox\Core\Repositories\AdminAccessRepositoryInterface;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\UserRole;

/**
 * Class AuthenticateAdminCP.
 */
class AuthenticateAdminCP extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request  $request
     * @param Closure  $next
     * @param string[] ...$guards
     *
     * @return mixed
     *
     * @throws AuthenticationException
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $user   = user();
        $accept = false;

        if ($user->hasRole(UserRole::ADMIN_USER)) {
            $accept = true;
        } elseif ($user->hasRole(UserRole::SUPER_ADMIN_USER)) {
            $accept = true;
        }

        if (!$accept) {
            abort(403, 'You have no permission to access.');
        }

        $this->logAccess($user, $request);

        return $next($request);
    }

    /**
     * Handle an unauthenticated user.
     *
     * @param Request      $request
     * @param array<mixed> $guards
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function unauthenticated($request, array $guards)
    {
        // No need to throw any.
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param Request $request
     *
     * @return void
     */
    protected function redirectTo($request)
    {
        /* @var array<string, mixed> $middlewares */
        $request->headers->set('Accept', 'application/json');
        abort(403);
    }

    protected function logAccess(User $user, Request $request): void
    {
        resolve(AdminAccessRepositoryInterface::class)->logAccess($user, ['ip' => $request->ip()]);
    }
}

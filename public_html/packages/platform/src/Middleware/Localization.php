<?php

namespace MetaFox\Platform\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use MetaFox\Core\Support\Facades\Language;

class Localization
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string  $separator
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        // todo should move Language::availableLocales() to config to optimize performance.
        $availableLocales = Language::availableLocales();

        if (($locale = $request->header('X-Language')) && is_string($locale) && in_array($locale, $availableLocales)) {
            // mobile locale first check.
            App::setLocale($locale);
        } elseif (($locale = $this->getUserLocale()) && in_array($locale, $availableLocales)) {
            // user locale check
            App::setLocale($locale);
        } elseif (($locale = $request->cookie(config('session.cookie_prefix').'userLanguage'))
            && in_array($locale, $availableLocales)) {
            App::setLocale($locale);
        } elseif (($locale = $this->getClientLocale($request->header('Accept-Language'), $availableLocales))) {
            App::setLocale($locale);
        }

        return $next($request);
    }

    protected function getClientLocale($accepts, $availableLocales)
    {
        $locales = array_reduce(
            explode(',', $accepts),
            function ($res, $el) {
                [$l, $q] = array_merge(explode(';q=', $el), [1]);
                $res[$l] = (float) $q;
                return $res;
            }, []);

        arsort($locales);
        $locales = array_intersect(array_keys($locales), $availableLocales);

        return $locales[0] ?? null;
    }

    protected function getUserLocale(): ?string
    {
        /** @var \MetaFox\User\Models\User $user */
        $id = Auth::id();

        if (!$id) {
            return null;
        }

        // should cache ?
        return DB::table('user_profiles')
            ->where('id', $id)
            ->value('language_id');
    }
}

<?php

/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Page\Listeners;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaFox\User\Models\UserEntity;

/**
 * Class PageRouteListener.
 * @ignore
 * @codeCoverageIgnore
 */
class PageRouteListener
{

    public function __construct()
    {
    }

    /**
     * @param  string  $url
     *
     * @return array<string,mixed>|void
     */
    public function handle(string $url)
    {
        if (!Str::startsWith($url, 'page/')) {
            return;
        }
        $code = Arr::last(explode('/', $url));
        
        /** @var UserEntity $user */
        $user = UserEntity::query()->where('user_name', '=', $code)->firstOrFail();

        $entityId = $user->entityId();
        $prefix = $user->entityType();

        return [
            'path' => "/$prefix/$entityId",
        ];
    }
}

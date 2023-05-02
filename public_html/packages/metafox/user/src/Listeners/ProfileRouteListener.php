<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Listeners;

use MetaFox\User\Models\UserEntity;

/**
 * Class ProfileRouteListener.
 * @ignore
 * @codeCoverageIgnore
 */
class ProfileRouteListener
{
    /**
     * @param string $url
     *
     * @return array<string,mixed>|null|void
     */
    public function handle(string $url)
    {
        try {
            $array = explode('/', $url);
            $name  = array_shift($array);

            /** @var UserEntity $user */
            $user = UserEntity::query()->where('user_name', '=', $name)->firstOrFail();

            $entityId = $user->entityId();
            $prefix   = $user->entityType();

            array_unshift($array, $entityId);
            array_unshift($array, $prefix);

            return [
                'path' => '/' . implode('/', $array),
            ];
        } catch (\Exception $exception) {
            // do nothing
        }
    }
}

<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\User\Listeners;

use Illuminate\Auth\Access\AuthorizationException;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

/**
 * Class UpdateProfileCoverListener.
 * @ignore
 * @codeCoverageIgnore
 */
class UpdateProfileCoverListener
{
    /**
     * @param  User                   $context
     * @param  User                   $owner
     * @param  array<string,mixed>    $attributes
     * @return array<string,mixed>
     * @throws AuthorizationException
     */
    public function handle(User $context, User $owner, array $attributes): array
    {
        return resolve(UserRepositoryInterface::class)
            ->updateCover($context, $owner, $attributes);
    }
}

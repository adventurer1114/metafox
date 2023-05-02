<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\FriendRequestRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use Metafox\User\Models\User;
use MetaFox\User\Support\Facades\UserEntity;

/**
 * @ignore
 * @codeCoverageIgnore
 */
class UserRegistrationExtraFieldsCreateListener
{
    public function handle($model)
    {
        if ($model instanceof User) {
            $ownerId = Settings::get('user.on_signup_new_friend');

            if ($ownerId) {
                $owner = UserEntity::getById($ownerId)->detail;

                resolve(FriendRequestRepositoryInterface::class)->sendRequest($owner, $model);
                resolve(FriendRepositoryInterface::class)->addFriend($owner, $model, true);
            }
        }
    }
}

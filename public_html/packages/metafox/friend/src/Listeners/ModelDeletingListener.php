<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Facades\Settings;
use MetaFox\User\Models\User;

class ModelDeletingListener
{
    private TagFriendRepositoryInterface $tagFriendRepository;

    /**
     * @param TagFriendRepositoryInterface $tagFriendRepository
     */
    public function __construct(TagFriendRepositoryInterface $tagFriendRepository)
    {
        $this->tagFriendRepository = $tagFriendRepository;
    }

    /**
     * @param mixed $model
     */
    public function handle($model): void
    {
        if ($model instanceof HasTaggedFriend) {
            $this->tagFriendRepository->deleteItemTagFriends($model);
        }

        if ($model instanceof User) {
            $ownerId = Settings::get('user.on_signup_new_friend');

            if ($ownerId == $model->entityId()) {
                Settings::reset(User::ENTITY_TYPE, ['user.on_signup_new_friend']);
            }
        }
    }
}

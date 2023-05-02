<?php

namespace MetaFox\Friend\Listeners;

use MetaFox\Friend\Repositories\FriendListRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Friend\Repositories\FriendRequestRepositoryInterface;
use MetaFox\Friend\Repositories\FriendTagBlockedRepositoryInterface;
use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UserDeletedListener
{
    public function handle(User $user): void
    {
        $this->deleteFriends($user);

        $this->deleteFriendLists($user);

        $this->deleteFriendRequests($user);

        $this->deleteFriendTags($user);

        $this->deleteBlockedFriendTags($user);

        $this->deleteIgnoredSuggestions($user);
    }

    protected function deleteIgnoredSuggestions(User $user): void
    {
        resolve(FriendRepositoryInterface::class)->deleteUserSuggestionIgnoreData($user->entityId());
    }

    protected function deleteBlockedFriendTags(User $user): void
    {
        resolve(FriendTagBlockedRepositoryInterface::class)->deleteUserData($user->entityId());
    }

    protected function deleteFriendTags(User $user): void
    {
        resolve(TagFriendRepositoryInterface::class)->deleteUserData($user->entityId());
    }

    protected function deleteFriendRequests(User $user): void
    {
        $repository = resolve(FriendRequestRepositoryInterface::class);

        $repository->deleteUserData($user->entityId());

        $repository->deleteOwnerData($user->entityId());
    }

    protected function deleteFriendLists(User $user): void
    {
        resolve(FriendListRepositoryInterface::class)->deleteUserData($user->entityId());
    }

    protected function deleteFriends(User $user): void
    {
        resolve(FriendRepositoryInterface::class)->deleteUserData($user->entityId());
    }
}

<?php

namespace MetaFox\Forum\Listeners;

use MetaFox\Forum\Repositories\ForumPostRepositoryInterface;
use MetaFox\Forum\Repositories\ForumThreadRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class UserDeletedListener
{
    public function handle(?User $user): void
    {
        if (!$user) {
            return;
        }
        $this->deleteThreads($user);

        $this->deletePosts($user);
    }

    protected function deleteThreads(User $user)
    {
        $threadService = resolve(ForumThreadRepositoryInterface::class);
        $threadService->deleteUserData($user);
        $threadService->deleteOwnerData($user);
    }

    protected function deletePosts(User $user)
    {
        $postService = resolve(ForumPostRepositoryInterface::class);
        $postService->deleteUserData($user);
        $postService->deleteOwnerData($user);
    }
}

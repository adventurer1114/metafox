<?php

namespace MetaFox\Poll\Listeners;

use MetaFox\Platform\Contracts\User;
use MetaFox\Poll\Repositories\PollRepositoryInterface;

class UserDeletedListener
{
    public function handle(User $user): void
    {
        $this->deletePolls($user);
    }

    protected function deletePolls(User $user): void
    {
        $repository = resolve(PollRepositoryInterface::class);

        $repository->deleteUserData($user);

        $repository->deleteOwnerData($user);
    }
}

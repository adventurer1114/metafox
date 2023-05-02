<?php

namespace MetaFox\Quiz\Listeners;

use MetaFox\Platform\Contracts\User;
use MetaFox\Quiz\Repositories\QuizRepositoryInterface;

class UserDeletedListener
{
    public function handle(User $user): void
    {
        $this->deleteQuizzes($user);
    }

    protected function deleteQuizzes(User $user): void
    {
        $repository = resolve(QuizRepositoryInterface::class);

        $repository->deleteUserData($user);

        $repository->deleteOwnerData($user);
    }
}

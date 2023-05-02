<?php

namespace MetaFox\Authorization\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;

class DeleteUsersByRoleJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected User $context, protected array $userIds)
    {
    }

    public function handle(): void
    {
        $repository = resolve(UserRepositoryInterface::class);

        foreach ($this->userIds as $userId) {
            $repository->deleteUser($this->context, $userId);
        }
    }
}

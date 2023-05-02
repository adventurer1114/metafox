<?php

namespace MetaFox\Friend\Listeners;

use Illuminate\Contracts\Pagination\Paginator;
use MetaFox\Friend\Policies\FriendListPolicy;
use MetaFox\Friend\Repositories\FriendListRepositoryInterface;
use MetaFox\Platform\Contracts\User;

class GetFriendListListener
{
    public function handle(?User $context, array $params = []): Paginator
    {
        policy_authorize(FriendListPolicy::class, 'viewAny', $context);

        return resolve(FriendListRepositoryInterface::class)->viewFriendLists($context, $params);
    }
}

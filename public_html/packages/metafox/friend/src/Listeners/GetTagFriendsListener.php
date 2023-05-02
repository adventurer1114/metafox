<?php

namespace MetaFox\Friend\Listeners;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\HasTaggedFriend;

/**
 * Class GetTagFriendsListener.
 * @ignore
 * @codeCoverageIgnore
 */
class GetTagFriendsListener
{
    /**
     * @param HasTaggedFriend $item
     * @param int             $limit
     *
     * @return LengthAwarePaginator
     */
    public function handle(HasTaggedFriend $item, int $limit): LengthAwarePaginator
    {
        return resolve(TagFriendRepositoryInterface::class)->getTagFriends($item, $limit);
    }
}

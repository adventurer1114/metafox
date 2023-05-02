<?php

namespace MetaFox\Friend\Repositories;

use MetaFox\Friend\Models\TagFriend;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface FriendTagBlocked.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface FriendTagBlockedRepositoryInterface
{
    /**
     * @param  TagFriend $tagFriend
     * @return bool
     */
    public function createTagBlocked(TagFriend $tagFriend): bool;

    /**
     * @param  int             $ownerId
     * @param  HasTaggedFriend $item
     * @return bool
     */
    public function isBlocked(int $ownerId, HasTaggedFriend $item): bool;

    /**
     * @param  int  $userId
     * @return void
     */
    public function deleteUserData(int $userId): void;
}

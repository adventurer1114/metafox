<?php

namespace MetaFox\Friend\Repositories;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use MetaFox\Friend\Models\TagFriend;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\User;
use MetaFox\User\Traits\UserMorphTrait;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface TagFriend.
 * @mixin BaseRepository
 * @method TagFriend find($id, $columns = ['*'])
 * @method TagFriend getModel()
 * @mixin UserMorphTrait
 */
interface TagFriendRepositoryInterface
{
    /**
     * @param HasTaggedFriend $item
     * @param int             $limit
     *
     * @return Builder
     */
    public function getTagFriends(HasTaggedFriend $item, int $limit): Builder;

    /**
     * @param  HasTaggedFriend $item
     * @param  User            $owner
     * @return TagFriend|null
     */
    public function getTagFriend(HasTaggedFriend $item, User $owner): ?TagFriend;

    /**
     * @param  Entity     $item
     * @return Collection
     */
    public function getAllTaggedFriends(Entity $item): Collection;

    /**
     * @param  HasTaggedFriend $item
     * @param  array|null      $friendIds
     * @return Collection
     */
    public function getItemTagFriends(HasTaggedFriend $item, ?array $friendIds = null): Collection;

    /**
     * $tagFriends = [
     *      [
     *          'friend_id' => 1,
     *          'px' => 1,
     *          'py' => 1,
     *      ],
     *      [ 'friend_id' => 1],
     *      ['friend_id' => 1, 'is_mention' => true, 'content' => 'user test ahihi'],
     * ];.
     *
     * @param  User                     $context
     * @param  HasTaggedFriend          $item
     * @param  array<string|int, mixed> $tagFriends
     * @return bool
     */
    public function createTagFriend(User $context, HasTaggedFriend $item, array $tagFriends): bool;

    /**
     * $tagFriends = [
     *      [
     *          'friend_id' => 1,
     *          'px' => 1,
     *          'py' => 1,
     *      ],
     *      [ 'friend_id' => 1],
     *      ['friend_id' => 1, 'is_mention' => true, 'content' => 'user test ahihi'],
     * ];.
     *
     * @param  User            $context
     * @param  HasTaggedFriend $item
     * @param  int[]           $tagFriends
     * @return bool
     */
    public function updateTagFriend(User $context, HasTaggedFriend $item, array $tagFriends): bool;

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteTagFriend(int $id): bool;

    /**
     * @param  HasTaggedFriend $item
     * @param  array|null      $friendIds
     * @return void
     */
    public function deleteItemTagFriends(HasTaggedFriend $item, ?array $friendIds = null): void;
}

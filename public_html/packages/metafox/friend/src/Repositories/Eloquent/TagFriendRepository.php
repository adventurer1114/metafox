<?php

namespace MetaFox\Friend\Repositories\Eloquent;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\JoinClause;
use MetaFox\Friend\Models\TagFriend;
use MetaFox\Friend\Repositories\FriendTagBlockedRepositoryInterface;
use MetaFox\Friend\Repositories\TagFriendRepositoryInterface;
use MetaFox\Platform\Contracts\ActivityFeedSource;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasPrivacyMember;
use MetaFox\Platform\Contracts\HasTaggedFriend;
use MetaFox\Platform\Contracts\HasTaggedFriendWithPosition;
use MetaFox\Platform\Contracts\User;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\User\Models\UserEntity as UserEntityModel;
use MetaFox\User\Support\Facades\UserBlocked;
use MetaFox\User\Support\Facades\UserEntity;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Traits\UserMorphTrait;

/**
 * Class TagFriendRepository.
 *
 * @method TagFriend find($id, $columns = ['*'])
 * @method TagFriend getModel()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @ignore
 * @codeCoverageIgnore
 */
class TagFriendRepository extends AbstractRepository implements TagFriendRepositoryInterface
{
    use UserMorphTrait;
    public function model(): string
    {
        return TagFriend::class;
    }

    /**
     * @return FriendTagBlockedRepositoryInterface
     */
    private function friendTagBlockedRepository(): FriendTagBlockedRepositoryInterface
    {
        return resolve(FriendTagBlockedRepositoryInterface::class);
    }

    /**
     * @param  HasTaggedFriend $item
     * @param  User            $owner
     * @return TagFriend|null
     */
    public function getTagFriend(HasTaggedFriend $item, User $owner): ?TagFriend
    {
        $data = [
            'owner_id'   => $owner->entityId(),
            'owner_type' => $owner->entityType(),
            'item_id'    => $item->entityId(),
            'item_type'  => $item->entityType(),
        ];

        /** @var TagFriend $tag */
        $tag = TagFriend::query()
            ->where($data)
            ->first();

        return $tag;
    }

    public function getTagFriends(HasTaggedFriend $item, int $limit): Builder
    {
        return UserEntityModel::query()
            ->join('friend_tag_friends', function (JoinClause $join) use ($item) {
                $join->on('user_entities.id', '=', 'friend_tag_friends.owner_id');
                $join->where('item_id', $item->entityId());
                $join->where('item_type', $item->entityType());
            })
            ->where('is_mention', '=', 0);
    }

    public function getAllTaggedFriends(Entity $item): Collection
    {
        return UserEntityModel::query()
            ->join('friend_tag_friends', function (JoinClause $join) use ($item) {
                $join->on('user_entities.id', '=', 'friend_tag_friends.owner_id');
                $join->where('item_id', $item->entityId());
                $join->where('item_type', $item->entityType());
            })
            ->get(['user_entities.*']);
    }

    /**
     * @param  HasTaggedFriend $item
     * @param  array|null      $friendIds
     * @return Collection
     */
    public function getItemTagFriends(HasTaggedFriend $item, ?array $friendIds = null): Collection
    {
        $query = $this->getModel()->newQuery()
            ->with(['ownerEntity', 'item'])
            ->where('item_id', $item->entityId())
            ->where('item_type', $item->entityType());

        if (is_array($friendIds) && count($friendIds)) {
            $query->whereIn('owner_id', $friendIds);
        }

        return $query->get();
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function createTagFriend(User $context, HasTaggedFriend $item, array $tagFriends): bool
    {
        if (empty($tagFriends)) {
            return false;
        }

        [$tagFriendIds, $tagFriends] = $this->handleInputTagFriend($tagFriends);

        $owner = null;

        if ($item instanceof Content) {
            $owner = $item->owner;
        }

        $friends = $this->filterTaggedPrivacy($context, $tagFriendIds, $owner);

        if (empty($friends)) {
            return false;
        }

        foreach ($friends as $friend) {
            /** @var TagFriend $tag */
            $tag = $this->getTagFriend($item, $friend);

            if (null != $tag) {
                if ($item instanceof HasTaggedFriendWithPosition) {
                    if ($tag->px != $tagFriends[$friend->entityId()]['px'] || $tag->py != $tagFriends[$friend->entityId()]['py']) {
                        $tag->update([
                            'px' => $tagFriends[$friend->entityId()]['px'],
                            'py' => $tagFriends[$friend->entityId()]['py'],
                        ]);
                    }
                }

                continue;
            }

            $data = [
                'owner_id'   => $friend->entityId(),
                'owner_type' => $friend->entityType(),
                'item_id'    => $item->entityId(),
                'item_type'  => $item->entityType(),
            ];

            /* @var User $friend */
            $tagFriendData = array_merge($data, [
                'user_id'    => $context->entityId(),
                'user_type'  => $context->entityType(),
                'px'         => $tagFriends[$friend->entityId()]['px'] ?? 0,
                'py'         => $tagFriends[$friend->entityId()]['py'] ?? 0,
                'is_mention' => $tagFriends[$friend->entityId()]['is_mention'] ?? false,
                'content'    => $tagFriends[$friend->entityId()]['content'] ?? null,
            ]);

            TagFriend::query()->create($tagFriendData);

            $this->handlePutToTagStream($context, $friend, $item);
        }

        return true;
    }

    /**
     * @inheritdoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function updateTagFriend(User $context, HasTaggedFriend $item, array $tagFriends): bool
    {
        [$tagFriendIds, $tagFriends] = $this->handleInputTagFriend($tagFriends);

        $owner = null;

        if ($item instanceof Content) {
            $owner = $item->owner;
        }

        $friends = $this->filterTaggedPrivacy($context, $tagFriendIds, $owner);

        $condition = [
            'item_id'   => $item->entityId(),
            'item_type' => $item->entityType(),
        ];

        $oldTagFriends = TagFriend::query()
            ->where($condition)
            ->get()
            ->collect();

        if (empty($friends) && $oldTagFriends->count() == 0) {
            return false;
        }

        $friendAfterFilers = array_keys($friends);

        $oldTagFriendIds = $oldTagFriends->pluck('owner_id')->toArray();

        $removeTagged = array_diff($oldTagFriendIds, $friendAfterFilers);

        $newTagged = array_diff($friendAfterFilers, $oldTagFriendIds);

        if (!empty($removeTagged)) {
            $removeTaggedReal = $removeTagged;

            TagFriend::query()
                ->whereIn('owner_id', $removeTaggedReal)
                ->where($condition)
                ->get()->each(function (TagFriend $tagFriend) use (&$removeTaggedReal) {
                    if ($tagFriend->px > 0 || $tagFriend->py > 0) {
                        foreach ($removeTaggedReal as $key => $friendId) {
                            if ($friendId == $tagFriend->ownerId()) {
                                unset($removeTaggedReal[$key]);
                            }
                        }
                    }
                });

            $this->handleDeleteFromTagStream($context, $removeTagged, $item);

            $this->deleteItemTagFriends($item, $removeTaggedReal);
        }

        if ($item instanceof HasTaggedFriendWithPosition) {
            $notChange = array_diff($oldTagFriendIds, $removeTagged, $newTagged);

            if (!empty($notChange)) {
                $oldTagFriendsArray = $oldTagFriends->pluck([], 'owner_id')->toArray();

                foreach ($notChange as $friendId) {
                    if (
                        $oldTagFriendsArray[$friendId]['px'] != $tagFriends[$friendId]['px']
                        || $oldTagFriendsArray[$friendId]['py'] != $tagFriends[$friendId]['py']
                    ) {
                        TagFriend::query()
                            ->where('id', $oldTagFriendsArray[$friendId]['id'])
                            ->update([
                                'px' => $tagFriends[$friendId]['px'],
                                'py' => $tagFriends[$friendId]['py'],
                            ]);
                    }
                }
            }
        }
        if (!empty($newTagged)) {
            foreach ($newTagged as $friendId) {
                /** @var User $friend */
                $friend = $friends[$friendId];

                $tagFriendData = [
                    'user_id'    => $context->entityId(),
                    'user_type'  => $context->entityType(),
                    'owner_id'   => $friend->entityId(),
                    'owner_type' => $friend->entityType(),
                    'item_id'    => $item->entityId(),
                    'item_type'  => $item->entityType(),
                    'px'         => $tagFriends[$friend->entityId()]['px'] ?? 0,
                    'py'         => $tagFriends[$friend->entityId()]['py'] ?? 0,
                    'is_mention' => $tagFriends[$friend->entityId()]['is_mention'] ?? false,
                    'content'    => $tagFriends[$friend->entityId()]['content'] ?? null,
                ];
                $isBlocked = $this->friendTagBlockedRepository()->isBlocked($friendId, $item);
                if ($isBlocked) {
                    if (($tagFriends[$friendId]['is_mention'] ?? false)) {
                        continue;
                    }
                    $message = json_encode([
                        'title'   => 'Notice',
                        'message' => __p('friend::phrase.error_tag_the_friends_again_on_this_post'),
                    ]);
                    abort(403, $message);
                }
                TagFriend::query()->create($tagFriendData);

                $this->handlePutToTagStream($context, $friend, $item);
            }
        }

        return true;
    }

    /**
     * @param  User              $context
     * @param  int[]             $tagFriends
     * @param  User|null         $owner
     * @return array<int, mixed>
     */
    private function filterTaggedPrivacy(User $context, array $tagFriends, ?User $owner = null): array
    {
        $friends = [];

        foreach ($tagFriends as $friendId) {
            $friend = UserEntity::getById($friendId)->detail;

            if (UserBlocked::isBlocked($context, $friend)) {
                continue;
            }

            //In case tagging page/group
            if ($friend instanceof HasPrivacyMember) {
                if ($context->can('view', [$friend, $friend])) {
                    $friends[$friendId] = $friend;
                }

                continue;
            }

            if (!UserPrivacy::hasAccess($context, $friend, 'user.can_i_be_tagged')) {
                continue;
            }

            //In case tagging members in page/group
            if (method_exists($owner, 'hasTaggedPermission')) {
                if (call_user_func([$owner, 'hasTaggedPermission'], $friend)) {
                    $friends[$friendId] = $friend;
                }

                continue;
            }

            $friends[$friendId] = $friend;
        }

        return $friends;
    }

    /**
     * @param array<string|int, mixed> $tagFriends
     *
     * @return array<int, mixed>
     */
    private function handleInputTagFriend(array $tagFriends): array
    {
        $tagFriendIds = [];

        foreach ($tagFriends as $index => $value) {
            unset($tagFriends[$index]);

            // @todo should not accept int here.
            if (!is_array($value)) {
                $tagFriendIds[]     = $value;
                $tagFriends[$value] = [
                    'friend_id' => $value,
                    'px'        => 0,
                    'py'        => 0,
                ];
                continue;
            }

            $tagFriendIds[] = $value['friend_id'];

            $tagFriends[$value['friend_id']] = [
                'friend_id'  => $value['friend_id'],
                'px'         => array_key_exists('px', $value) ? $value['px'] : 0,
                'py'         => array_key_exists('py', $value) ? $value['py'] : 0,
                'is_mention' => $value['is_mention'] ?? 0,
                'content'    => $value['content'] ?? null,
            ];
        }

        $tagFriendIds = array_unique($tagFriendIds);

        return [$tagFriendIds, $tagFriends];
    }

    /**
     * @param int $id
     *
     * @return bool
     */
    public function deleteTagFriend(int $id): bool
    {
        $taggedFriend = $this->find($id);

        $item = $taggedFriend->item;

        if (!$item instanceof HasTaggedFriend) {
            return false;
        }

        $this->friendTagBlockedRepository()->createTagBlocked($taggedFriend);

        $this->handleDeleteFromTagStream($taggedFriend->user, [$taggedFriend->owner_id], $item);

        return (bool) $taggedFriend->delete();
    }

    /**
     * @param  HasTaggedFriend $item
     * @param  array|null      $friendIds
     * @return void
     */
    public function deleteItemTagFriends(HasTaggedFriend $item, ?array $friendIds = null): void
    {
        $tagFriends = $this->getItemTagFriends($item, $friendIds);

        foreach ($tagFriends as $tagFriend) {
            $tagFriend->delete();
        }
    }

    private function handlePutToTagStream(User $context, User $friend, HasTaggedFriend $item)
    {
        if (!$item->hasTagStream()) {
            return;
        }

        if ($item->owner instanceof HasPrivacyMember) {
            return;
        }

        if (!$item instanceof ActivityFeedSource) {
            return;
        }

        $feedAction = $item->toActivityFeed();

        app('events')->dispatch(
            'activity.feed_put_to_tag_stream',
            [
                $context,
                $friend,
                $item->entityId(),
                $item->entityType(),
                $feedAction->getTypeId(),
            ],
            true
        );
    }

    private function handleDeleteFromTagStream(User $context, array $friendIds, HasTaggedFriend $item): void
    {
        if (!$item->hasTagStream()) {
            return;
        }

        if (!$item instanceof ActivityFeedSource) {
            return;
        }

        $feedAction = $item->toActivityFeed();

        foreach ($friendIds as $friendId) {
            app('events')->dispatch(
                'activity.feed_delete_from_tag_stream',
                [
                    $context,
                    $friendId,
                    $item->entityId(),
                    $item->entityType(),
                    $feedAction->getTypeId(),
                ],
                true
            );
        }
    }
}

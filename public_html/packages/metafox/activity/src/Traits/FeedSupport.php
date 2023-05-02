<?php

namespace MetaFox\Activity\Traits;

use Illuminate\Contracts\Database\Eloquent\Builder;
use MetaFox\Activity\Contracts\ActivityHiddenManager;
use MetaFox\Activity\Contracts\ActivitySnoozeManager;
use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Type;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\Entity;
use MetaFox\Platform\Contracts\HasBackGroundStatus;
use MetaFox\Platform\Contracts\HasLocationCheckin;
use MetaFox\Platform\Contracts\HasTotalComment;
use MetaFox\Platform\Contracts\HasTotalCommentWithReply;
use MetaFox\Platform\Contracts\HasTotalLike;
use MetaFox\Platform\Contracts\HasTotalShare;
use MetaFox\Platform\Contracts\HasTotalView;
use MetaFox\Platform\Traits\Helpers\IsFriendTrait;
use MetaFox\Platform\Traits\Helpers\IsLikedTrait;
use MetaFox\Platform\Traits\Helpers\UserReactedTrait;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;

/**
 * @property Feed $resource
 */
trait FeedSupport
{
    use HasStatistic;
    use IsFriendTrait {
        IsFriendTrait::getTaggedFriends as getTaggedFriendsTrait;
    }
    use FeedExtra;
    use IsLikedTrait;
    use UserReactedTrait;
    use RelatedCommentsTrait;
    use HasTagTrait;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        $item = $this->getActionResource();

        $react = $item;

        // In case some entities has feed but not really a content
        if ($item instanceof Content) {
            $react = $item->reactItem();
        }

        return [
            'total_like'    => $react instanceof HasTotalLike ? $react->total_like : 0,
            'total_comment' => $react instanceof HasTotalComment ? $react->total_comment : 0,
            'total_reply'   => $react instanceof HasTotalCommentWithReply ? $react->total_reply : 0,
            'total_view'    => $react instanceof HasTotalView ? $react->total_view : 0,
            'total_share'   => $react instanceof HasTotalShare ? $react->total_share : 0,
        ];
    }

    protected function getTypeManager(): TypeManager
    {
        return resolve(TypeManager::class);
    }

    protected function getHideFeedService(): ActivityHiddenManager
    {
        return resolve(ActivityHiddenManager::class);
    }

    protected function getHideAllService(): ActivitySnoozeManager
    {
        return resolve(ActivitySnoozeManager::class);
    }

    protected function getActionResource(): Entity
    {
        $result = $this->getTypeManager()->hasFeature($this->resource->type_id, Type::ACTION_ON_FEED_TYPE)
            ? $this->resource
            : $this->resource->item;

        if (!$result) {
            $result = $this->resource;
        }

        return $result;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function getLocation(): ?array
    {
        $item = $this->getActionResource();

        $location = null;

        if ($item instanceof HasLocationCheckin) {
            [$address, $lat, $lng] = $item->toLocation();
            if ($address && $lat && $lng) {
                $location = [
                    'address' => $address,
                    'lat'     => (float) $lat,
                    'lng'     => (float) $lng,
                ];
            }
        }

        return $location;
    }

    /**
     * @return mixed
     */
    protected function getBackgroundStatus()
    {
        $item = $this->getActionResource();

        $statusBackground = null;

        if ($item instanceof HasBackGroundStatus) {
            $statusBackground = $item->getBackgroundStatusImage();
        }

        return $statusBackground;
    }

    /**
     * @return array<mixed>
     */
    protected function getTaggedFriends(int $limit = 10): array
    {
        $taggedFriends      = [];
        $totalFriendsTagged = 0;

        $item = $this->getActionResource();

        if (!$item instanceof Entity) {
            return [$taggedFriends, $totalFriendsTagged];
        }

        $taggedFriendsQuery = $this->getTaggedFriendsTrait($item, $limit);

        if (!$taggedFriendsQuery instanceof Builder) {
            return[$taggedFriends, $totalFriendsTagged];
        }

        $taggedFriendsData = $taggedFriendsQuery->paginate($limit, ['user_entities.*'], 'tag_friend_page');

        if (!empty($taggedFriendsData)) {
            $taggedFriends      = $taggedFriendsData->items();
            $totalFriendsTagged = $taggedFriendsData->total();
        }

        return [$taggedFriends, $totalFriendsTagged];
    }
}

<?php

namespace MetaFox\Activity\Http\Resources\v1\Feed;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Share;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Activity\Traits\FeedSupport;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Contracts\User as UserContract;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityCollection;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Models\User;
use MetaFox\User\Models\UserEntity;

/**
 * Class FeedItem.
 *
 * Do not use Gate in here to improve performance.
 *
 * @property Feed $resource
 */
class FeedItem extends JsonResource
{
    use FeedSupport;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function toArray($request): array
    {
        $profileId = $request->get('user_id', 0);

        $item        = $this->resource->item;
        $actionItem  = $this->getActionResource();
        $context     = user();
        $postOnOther = $this->resource->userId() != $this->resource->ownerId();
        $userEntity  = $this->resource->userEntity;
        $ownerEntity = $this->resource->ownerEntity;

        $userResource = new UserEntityDetail($userEntity);

        $ownerResource = null;

        if ($postOnOther) {
            $ownerResource = new UserEntityDetail($ownerEntity);
        }

        $actionPhrase = $this->getTypeManager()->getTypePhraseWithContext($this->resource);

        [$taggedFriends, $totalFriendsTagged] = $this->getTaggedFriends();

        $user = $this->resource->user;

        $owner = $this->resource->owner;

        $reactItem = $actionItem;

        if ($actionItem instanceof Content) {
            $reactItem = $actionItem->reactItem();
        }

        $isOwnerTagged = false;

        if (!$postOnOther) {
            $isOwnerTagged = $this->isTagged($owner, $profileId, $taggedFriends);
        }

        $isShowLocation = true;

        if (method_exists($reactItem, 'isShowLocation')) {
            $isShowLocation = $reactItem->isShowLocation();
        }

        $data = [
            'id'                         => $this->resource->entityId(),
            'module_name'                => $this->resource->entityType(),
            'resource_name'              => $this->resource->entityType(),
            'type_id'                    => $this->resource->type_id,
            'like_type_id'               => $reactItem->entityType(),
            'like_item_id'               => $reactItem->entityId(),
            'comment_type_id'            => $reactItem->entityType(),
            'comment_item_id'            => $reactItem->entityId(),
            'item_type'                  => $this->resource->itemType(),
            'item_id'                    => $this->resource->itemId(),
            'info'                       => $actionPhrase,
            'status'                     => $this->getParsedContent(),
            'invisible'                  => $user instanceof User ? $user->is_invisible : false,
            'tagged_friends'             => new UserEntityCollection($taggedFriends),
            'total_friends_tagged'       => $totalFriendsTagged,
            'location'                   => $this->getLocation(),
            'sponsor_id'                 => null,
            'click_ref'                  => null, // @todo ??
            'user'                       => $userResource,
            'statistic'                  => $this->getStatistic(),
            'embed_object'               => ResourceGate::asEmbed($item),
            'parent_user'                => $ownerResource,
            'privacy'                    => $this->resource->privacy,
            'like_phrase'                => null, // @todo not used, consider to remove.
            'is_shared_feed'             => $this->resource->total_share > 0,
            'is_hidden'                  => $this->getHideFeedService()->isHide($context, $this->resource),
            'is_hidden_all'              => $this->getHideAllService()->isHideAll($context, $owner),
            'is_just_hide'               => false,
            'is_just_remove_tag'         => false,
            'is_show_location'           => $isShowLocation,
            'user_full_name'             => $userEntity instanceof UserEntity ? $userEntity->name : null,
            'owner_full_name'            => $ownerEntity instanceof UserEntity ? $ownerEntity->name : null,
            'creation_date'              => $this->resource->created_at,
            'modification_date'          => $this->resource->updated_at,
            'link'                       => $this->resource->toLink(),
            'url'                        => $this->resource->toUrl(),
            'extra'                      => $this->getFeedExtra(),
            'is_saved'                   => $context->can('isSavedItem', [Feed::class, $this->resource]),
            'status_background'          => $this->getBackgroundStatus(),
            'is_liked'                   => $this->isLike($context, $reactItem),
            'is_pending'                 => $this->resource->is_pending,
            'related_comments'           => $this->relatedComments($context, $reactItem),
            'related_comments_statistic' => $this->relatedCommentsHiddenStatistics($context, $reactItem),
            'relevant_comments'          => null,
            'user_reacted'               => $this->userReacted($context, $reactItem),
            'most_reactions'             => $this->userMostReactions($context, $reactItem),
            'privacy_detail'             => ActivityFeed::getPrivacyDetail(
                $context,
                $this->resource,
                $this->resource->owner?->getRepresentativePrivacy()
            ),
            'pins'            => app('activity.pin')->getPinOwnerIds($context, $this->resource->id),
            'is_owner_tagged' => $isOwnerTagged,
        ];

        // Get sharedUser, sharedOwner full name
        if ($item instanceof Share) {
            $item->loadMissing(['item']);
            $content = $item->item;

            if ($content instanceof Content) {
                $userEntity  = $content->userEntity;
                $ownerEntity = $content->ownerEntity;

                $data['shared_user_full_name']  = $userEntity instanceof UserEntity ? $userEntity->name : null;
                $data['shared_owner_full_name'] = $ownerEntity instanceof UserEntity ? $ownerEntity->name : null;
            }
        }

        return $data;
    }

    protected function isTagged(UserContract $owner, int $profileId, array $taggedFriends = []): bool
    {
        if ($profileId == 0) {
            return false;
        }

        if ($owner->entityId() == $profileId) {
            return false;
        }

        $collection = collect($taggedFriends);

        return $collection->contains('id', '=', $profileId);
    }
}

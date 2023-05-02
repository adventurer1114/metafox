<?php

namespace MetaFox\Activity\Http\Resources\v1\Feed;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Activity\Contracts\TypeManager;
use MetaFox\Activity\Models\Feed;
use MetaFox\Activity\Models\Share;
use MetaFox\Activity\Support\Facades\ActivityFeed;
use MetaFox\Activity\Traits\FeedSupport;
use MetaFox\Platform\Contracts\Content;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityCollection;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Models\UserEntity;

/*
|--------------------------------------------------------------------------
| Resource Embed
|--------------------------------------------------------------------------
|
| Resource embed is used when you want attach this resource as embed content of
| activity feed, notification, ....
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
*/

/**
 * Class FeedEmbed.
 * @property Feed $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class FeedEmbed extends JsonResource
{
    use FeedSupport;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $item = $this->resource->item;

        $userEntity   = $this->resource->userEntity;
        $ownerEntity  = $this->resource->ownerEntity;
        $userResource = new UserEntityDetail($this->resource->userEntity);

        $postOnOther = $this->resource->userId() != $this->resource->ownerId();

        $ownerResource = null;
        if ($postOnOther) {
            $ownerResource = new UserEntityDetail($this->resource->ownerEntity);
        }

        $actionPhrase = null;
        if (!$postOnOther) {
            /** @var TypeManager $activityTypeManager */
            $activityTypeManager = resolve(TypeManager::class);
            $actionPhrase        = $activityTypeManager->getTypePhraseWithContext($this->resource);
        }

        [$taggedFriends, $totalFriendsTagged] = $this->getTaggedFriends();

        $react = $this->getActionResource();

        if ($react instanceof Content) {
            $react = $react->reactItem();
        }

        $context = user();

        $data = [
            'id'                   => $this->resource->entityId(),
            'module_name'          => $this->resource->entityType(),
            'resource_name'        => $this->resource->entityType(),
            'type_id'              => $this->resource->type_id,
            'like_type_id'         => $react->entityType(),
            'like_item_id'         => $react->entityId(),
            'comment_type_id'      => $react->entityType(),
            'comment_item_id'      => $react->entityId(),
            'item_type'            => $this->resource->itemType(),
            'item_id'              => $this->resource->itemId(),
            'info'                 => $actionPhrase,
            'status'               => $this->getParsedContent(),
            'tagged_friends'       => new UserEntityCollection($taggedFriends),
            'total_friends_tagged' => $totalFriendsTagged,
            'location'             => $this->getLocation(),
            'statistic'            => $this->getStatistic(),
            'user'                 => $userResource,
            'embed_object'         => ResourceGate::asEmbed($item),
            'parent_user'          => $ownerResource,
            'privacy'              => $this->resource->privacy,
            'user_full_name'       => $userEntity instanceof UserEntity ? $userEntity->name : null,
            'owner_full_name'      => $ownerEntity instanceof UserEntity ? $ownerEntity->name : null,
            'creation_date'        => $this->resource->created_at,
            'modification_date'    => $this->resource->updated_at,
            'link'                 => $this->resource->toLink(),
            'url'                  => $this->resource->toUrl(),
            'extra'                => $this->getFeedExtra(),
            'status_background'    => $this->getBackgroundStatus(),
            'privacy_detail'       => ActivityFeed::getPrivacyDetail($context, $this->resource, $this->resource->owner?->getRepresentativePrivacy()),
        ];

        if ($item instanceof Share) {
            $item->loadMissing(['item']);
            $content      = $item->item;
            $data['link'] = $this->resource->toLink();
            $data['url']  = $this->resource->toUrl();

            if ($content instanceof Content) {
                $data['link'] = $content->toLink();
                $data['url']  = $content->toUrl();
            }
        }

        return $data;
    }
}

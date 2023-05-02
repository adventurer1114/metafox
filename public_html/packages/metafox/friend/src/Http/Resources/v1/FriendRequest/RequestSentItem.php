<?php

namespace MetaFox\Friend\Http\Resources\v1\FriendRequest;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Arr;
use MetaFox\Friend\Models\FriendRequest as Model;
use MetaFox\Friend\Repositories\FriendRepositoryInterface;
use MetaFox\Friend\Support\Facades\Friend;
use MetaFox\Friend\Support\Traits\HasFriendExtraTrait;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\Platform\Screen;
use MetaFox\User\Http\Resources\v1\User\UserItem;

/**
 * Class RequestSentItem.
 * @property Model $resource
 */
class RequestSentItem extends JsonResource
{
    use HasFriendExtraTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     * @throws AuthenticationException
     */
    public function toArray($request): array
    {
        $friendRepository = resolve(FriendRepositoryInterface::class);

        $context = user();
        $user    = $this->resource?->owner;

        $userResource = new UserItem($user);
        $userResource = $userResource->toArray($request);
        if ($user) {
            $userResource['statistic'] = [
                'total_friend' => $friendRepository->countTotalFriends($user->entityId()),
                'total_mutual' => $friendRepository->countMutualFriends($context->entityId(), $user->entityId()),
            ];
        }

        return [
            'resource_name'  => $this->resource?->entityType(),
            'is_seen'        => $this->resource?->is_seen,
            'is_ignore'      => $this->resource?->is_ignore,
            'message'        => null,
            'user'           => $userResource,
            'mutual_friends' => [
                'total'   => Arr::get($userResource, 'statistic.total_friend', 0),
                'friends' => [],
            ],
            'friendship'        => $user ? Friend::getFriendship($context, $user) : null,
            'accept_action'     => Screen::ACTION_ACCEPT_FRIEND,
            'deny_action'       => Screen::ACTION_DENY_FRIEND,
            'module'            => \MetaFox\Friend\Models\Friend::ENTITY_TYPE,
            'id'                => $this->resource?->entityId(),
            'creation_date'     => $this->resource?->created_at,
            'modification_date' => $this->resource?->updated_at,
            'link'              => $this->resource ? url_utility()->makeApiResourceUrl($this->resource->entityType(), $this->resource->entityId()) : null,
            'url'               => $this->resource ? url_utility()->makeApiResourceFullUrl($this->resource->entityType(), $this->resource->entityId()) : null,
            'extra'             => $this->getExtra(),
            'privacy'           => MetaFoxPrivacy::EVERYONE,
        ];
    }
}

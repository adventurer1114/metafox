<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Support\Facades\Timezone;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Repositories\CustomFieldRepositoryInterface;
use MetaFox\User\Support\Browse\Traits\User\ExtraTrait;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Support\Facades\UserBlocked;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Traits\FriendStatisticTrait;
use MetaFox\User\Traits\UserLocationTrait;
use MetaFox\User\Traits\UserStatisticTrait;

/**
 * Class UserItem.
 *
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserItem extends JsonResource
{
    use ExtraTrait;
    use UserStatisticTrait;
    use UserLocationTrait;
    use FriendStatisticTrait;

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
        $context = user();
        $profile = $this->resource->profile;

        $summary = null;
        if (UserPrivacy::hasAccess($context, $this->resource, 'profile.profile_info') === true) {
            $summary = UserFacade::getSummary($context, $this->resource);
        }

        $data = [
            'id'                   => $this->resource->entityId(),
            'module_name'          => $this->resource->entityType(),
            'resource_name'        => $this->resource->entityType(),
            'full_name'            => $this->resource->full_name,
            'user_name'            => $this->resource->user_name,
            'avatar'               => $profile->avatars,
            'avatar_id'            => $profile->getAvatarId(),
            'last_name'            => UserFacade::getLastName($this->resource->full_name),
            'first_name'           => UserFacade::getFirstName($this->resource->full_name),
            'gender'               => $profile->gender,
            'language_id'          => $profile->language_id,
            'joined'               => $this->resource->created_at, // formatted to ISO-8601 as v4
            'time_zone'            => Timezone::getName($profile->timezone_id),
            'default_currency'     => $profile->currency_id,
            'cover'                => $profile->covers,
            'cover_photo_id'       => $profile->getCoverId(),
            'cover_photo_position' => $profile->cover_photo_position,
            'post_types'           => [],
            'summary'              => $summary,
            'activity_total'       => 0,
            'activity_points'      => 0,
            'is_featured'          => $this->resource->is_featured,
            'age'                  => UserFacade::getAge($this->resource->profile->birthday),
            'is_blocked'           => $this->isBlocked(),
            'short_name'           => UserFacade::getShortName($this->resource->full_name),
            'creation_date'        => $this->resource->created_at,
            'modification_date'    => $this->resource->updated_at,
            'link'                 => $this->resource->toLink(),
            'url'                  => $this->resource->toUrl(),
            'statistic'            => $this->getStatistic(),
            'friend_statistic'     => $this->getFriendStatistic(),
            'extra'                => $this->getExtra(),
            'friends'              => [],
            'privacy'              => 0,
            'is_owner'             => $profile->isOwner($context),
            'status_id'            => 0,
            'message'              => '',
            'friendship'           => UserFacade::getFriendship($context, $this->resource),
            'profile'              => $this->resource->customProfile(),
        ];

        $data = array_merge($data, $this->getLocation($context, $this->resource));

        return $data;
    }

    /**
     * @return bool
     * @throws AuthenticationException
     */
    protected function isBlocked()
    {
        return UserBlocked::isBlocked($this->resource, user());
    }
}

<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Platform\MetaFoxPrivacy;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Traits\UserLocationTrait;
use MetaFox\User\Traits\UserStatisticTrait;

/**
 * Class UserPreview.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserPreview extends JsonResource
{
    use UserStatisticTrait;
    use UserLocationTrait;

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
        $profile = $this->resource->profile;
        $context = user();

        $summary = null;
        if (UserPrivacy::hasAccess($context, $this->resource, 'profile.profile_info') === true) {
            $summary = UserFacade::getSummary($context, $this->resource);
        }

        return [
            'id'                   => $this->resource->entityId(),
            'module_name'          => $this->resource->entityType(),
            'resource_name'        => $this->resource->entityType(),
            'full_name'            => $this->resource->full_name,
            'avatar'               => $profile->avatar,
            'cover'                => $profile->cover,
            'cover_photo_position' => $profile->cover_photo_position,
            'statistic'            => $this->getStatistic(),
            'friendship'           => UserFacade::getFriendship($context, $this->resource),
            'privacy'              => MetaFoxPrivacy::EVERYONE,
            'description'          => $summary,
            'age'                  => UserFacade::getAge($profile->birthday),
            'new_age_phrase'       => UserFacade::getNewAgePhrase($profile->birthday),
            'location'             => $this->getLocation($context, $this->resource),
            'is_deleted'           => $this->resource->isDeleted(),
        ];
    }
}

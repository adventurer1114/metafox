<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Support\Browse\Traits\User\ExtraTrait;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Traits\UserStatisticTrait;

/**
 * Class UserEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserEmbed extends JsonResource
{
    use ExtraTrait;
    use UserStatisticTrait;

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

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->entityType(),
            'resource_name' => $this->resource->entityType(),
            'full_name'     => $this->resource->full_name,
            'user_name'     => $this->resource->user_name,
            'avatar'        => $profile->avatar,
            'friendship'    => UserFacade::getFriendship($context, $this->resource),
            'short_name'    => UserFacade::getShortName($this->resource->full_name),
            'summary'       => $summary,
            'link'          => $this->resource->toLink(),
            'url'           => $this->resource->toUrl(),
            'is_owner'      => $profile->isOwner($context),
            'statistic'     => $this->getStatistic(),
            'extra'         => $this->getExtra(),
            'is_deleted'    => $this->resource->isDeleted(),
        ];
    }
}

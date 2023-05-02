<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Page\Http\Resources\v1\Traits\IsUserInvited;
use MetaFox\Page\Http\Resources\v1\Traits\PageHasExtra;
use MetaFox\Page\Models\Page as Model;
use MetaFox\Page\Support\Facade\PageMembership;
use MetaFox\Platform\Traits\Http\Resources\HasStatistic;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Support\Facades\User;

/**
 * Class PageEmbed.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class PageEmbed extends JsonResource
{
    use PageHasExtra;
    use HasStatistic;
    use IsUserInvited;

    /**
     * @return array<string, mixed>
     */
    protected function getStatistic(): array
    {
        return [
            'total_like' => $this->resource->total_member,
        ];
    }

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
        $avatars = $this->resource->avatars;

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->entityType(),
            'resource_name' => $this->resource->entityType(),
            'title'         => $this->resource->name,
            'full_name'     => $this->resource->name,
            'user_name'     => $this->resource->profile_name,
            'is_liked'      => $this->resource->isMember($context),
            'is_member'     => $this->resource->isMember($context),
            'is_owner'      => $this->resource->isUser($context),
            'membership'    => PageMembership::getMembership($this->resource, $context),
            'is_admin'      => $this->resource->isAdmin($context),
            'is_invited'    => !$this->isUserInvited($context),
            'is_featured'   => (bool) $this->resource->is_featured,
            'is_sponsor'    => (bool) $this->resource->is_sponsor,
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'image'         => $avatars,
            'image_id'      => $this->resource->getAvatarId(),
            'avatar'        => $avatars,
            'avatar_id'     => $this->resource->getAvatarId(),
            'short_name'    => User::getShortName($this->resource->name),
            'summary'       => $this->resource->summary,
            'link'          => $this->resource->toLink(),
            'url'           => $this->resource->toUrl(),
            'extra'         => $this->getExtra(),
            'statistic'     => $this->getStatistic(),
        ];
    }
}

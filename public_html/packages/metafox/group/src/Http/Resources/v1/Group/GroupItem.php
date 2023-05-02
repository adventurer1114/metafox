<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Support\Browse\Traits\Group\StatisticTrait;
use MetaFox\Group\Support\GroupRole;
use MetaFox\Group\Support\InviteType;
use MetaFox\Group\Support\Membership;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Support\Facades\User;

/**
 * Class GroupItem.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class GroupItem extends JsonResource
{
    use StatisticTrait;
    use HasExtra;

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
        $covers = $this->resource->covers;

        $isMember = $this->resource->isMember(user());

        $isApproved = $this->resource->is_approved;

        $isPending = false;

        if (!$isApproved) {
            $isPending = true;
        }

        return [
            'id'                            => $this->resource->entityId(),
            'module_name'                   => $this->resource->entityType(),
            'resource_name'                 => $this->resource->entityType(),
            'title'                         => $this->resource->name,
            'privacy'                       => $this->resource->privacy,
            'reg_method'                    => $this->resource->privacy_type,
            'reg_name'                      => __p(PrivacyTypeHandler::PRIVACY_PHRASE[$this->resource->privacy_type]),
            'view_id'                       => $this->resource->is_approved ? 0 : 1,
            'image'                         => $covers,
            'cover'                         => $covers,
            'has_membership_question'       => Membership::hasMembershipQuestion($this->resource),
            'is_rule_confirmation'          => $this->resource->is_rule_confirmation,
            'is_answer_membership_question' => $this->resource->is_answer_membership_question,
            'cover_photo_position'          => $this->resource->cover_photo_position,
            'cover_photo_id'                => $this->resource->getCoverId(),
            'is_liked'                      => $isMember,
            'is_pending'                    => $isPending,
            'is_member'                     => $isMember,
            'membership'                    => Membership::getMembership($this->resource, user(),InviteType::INVITED_MEMBER),
            'is_featured'                   => $this->resource->is_featured,
            'is_sponsor'                    => $this->resource->is_sponsor,
            'short_name'                    => User::getShortName($this->resource->name),
            'defaultActiveTabMenu'          => $this->resource->landing_page,
            'user'                          => new UserEntityDetail($this->resource->userEntity),
            'link'                          => $this->resource->toLink(),
            'url'                           => $this->resource->toUrl(),
            'statistic'                     => $this->getStatistic(),
            'extra'                         => $this->getGroupExtra(),
        ];
    }

    /**
     * @return array<string,           bool>
     * @throws AuthenticationException
     */
    public function getGroupExtra(): array
    {
        $extra = $this->getExtra();

        $customExtra = GroupRole::getGroupSettingPermission(user(), $this->resource);

        return array_merge($extra, $customExtra);
    }
}

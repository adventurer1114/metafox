<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Group\Http\Resources\v1\Category\CategoryEmbed;
use MetaFox\Group\Http\Resources\v1\Invite\InviteItem;
use MetaFox\Group\Models\Group as Model;
use MetaFox\Group\Support\Browse\Traits\Group\StatisticTrait;
use MetaFox\Group\Support\Facades\Group as GroupFacade;
use MetaFox\Group\Support\GroupRole;
use MetaFox\Group\Support\InviteType;
use MetaFox\Group\Support\Membership;
use MetaFox\Group\Support\PrivacyTypeHandler;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\Traits\Http\Resources\HasExtra;
use MetaFox\User\Http\Resources\v1\UserEntity\UserEntityDetail;
use MetaFox\User\Support\Facades\User;
use MetaFox\User\Support\Facades\UserPrivacy;

/**
 * Class GroupDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GroupDetail extends JsonResource
{
    use HasExtra;
    use StatisticTrait;

    private ?string $inviteCode = null;
    protected const DEFAULT_TAB = 'about';

    public function setInviteCode(string $inviteCode): self
    {
        $this->inviteCode = $inviteCode;

        return $this;
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
        $covers     = $this->resource->covers;
        $context    = user();
        $inviteType = null;
        if ($this->inviteCode !== null) {
            $inviteType = InviteType::INVITED_GENERATE_LINK;
        }
        $membership = Membership::getMembership($this->resource, $context, $inviteType);

        $isLiked     = false;
        $isModerator = false;
        $isAdmin     = $this->resource->isAdmin($context);
        $isMember    = $this->resource->isMember($context);
        if ($membership == Membership::JOINED) {
            $isLiked     = true;
            $isModerator = true;
            if (!$isAdmin) {
                $isModerator = $this->resource->isModerator($context);
            }
        }

        $groupText            = $this->resource->groupText;
        $shortDescription     = $text = MetaFoxConstant::EMPTY_STRING;
        $regName              = __p(PrivacyTypeHandler::PRIVACY_PHRASE[$this->resource->privacy_type]);
        $defaultActiveTabMenu = $this->resource->landing_page;

        if ($groupText) {
            $text             = $groupText->text;
            $shortDescription = parse_output()->getDescription($groupText->text_parsed);
        }

        /** @var mixed $countPendingPost */
        $countPendingPost = app('events')->dispatch(
            'activity.count_feed_pending_on_owner',
            [$context, $this->resource],
            true
        );
        if (!is_numeric($countPendingPost)) {
            $countPendingPost = 0;
        }

        $pendingInvite = Membership::getAvailableInvite($this->resource, $context, $this->inviteCode);

        $isMuted = Membership::isMuted($this->resource->entityId(), $context->entityId());

        $privacyDetail = app('events')->dispatch(
            'activity.get_privacy_detail_on_owner',
            [$context, $this->resource],
            true
        );

        if (!$isLiked && !$this->resource->isPublicPrivacy()) {
            $defaultActiveTabMenu = self::DEFAULT_TAB;
        }

        $isApproved = $this->resource->is_approved;

        $isPending = $isViewId = false;

        if (!$isApproved) {
            $isPending = $isViewId = true;
        }

        return [
            'id'            => $this->resource->entityId(),
            'module_name'   => $this->resource->entityType(),
            'resource_name' => $this->resource->entityType(),
            'title'         => $this->resource->name,
            'privacy'       => $this->resource->privacy,
            'reg_method'    => $this->resource->privacy_type,
            'reg_name'      => $regName,
            'category'      => new CategoryEmbed($this->resource->category),
            'user'          => new UserEntityDetail($this->resource->userEntity),
            'text'          => $text,
            'description'   => $shortDescription,
            'view_id'       => $isViewId,
            'is_liked'      => $isLiked,
            'is_member'     => $isMember,
            'is_admin'      => $isAdmin,
            'is_moderator'  => $isModerator,
            'is_owner'      => user()->entityId() == $this->resource->userId(),
            'is_reg'        => null,
            'invite'        => new InviteItem(Membership::getPendingInvite(
                $this->resource,
                user(),
                $inviteType
            )),
            'is_pending'                    => $isPending,
            'is_featured'                   => $this->resource->is_featured,
            'is_sponsor'                    => $this->resource->is_sponsor,
            'is_following'                  => GroupFacade::isFollowing($context, $this->resource),
            'pending_mode'                  => $this->resource->pending_mode,
            'membership'                    => $membership,
            'image'                         => $covers,
            'cover'                         => $covers,
            'cover_photo_position'          => $this->resource->cover_photo_position,
            'cover_photo_id'                => $this->resource->getCoverId(),
            'latitude'                      => $this->resource->location_latitude,
            'longitude'                     => $this->resource->location_longitude,
            'location_name'                 => $this->resource->location_name,
            'item_type'                     => $this->resource->entityType(),
            'defaultActiveTabMenu'          => $defaultActiveTabMenu,
            'type_name'                     => '',
            'short_name'                    => User::getShortName($this->resource->name),
            'link'                          => $this->resource->toLink(),
            'url'                           => $this->resource->toUrl(),
            'pending_post_count'            => $countPendingPost,
            'creation_date'                 => $this->resource->created_at,
            'modification_date'             => $this->resource->updated_at,
            'profile_name'                  => $this->resource->profile_name,
            'statistic'                     => $this->getStatistic(),
            'extra'                         => $this->getGroupExtra(),
            'roles'                         => GroupRole::getGroupRolesByUser(user(), $this->resource),
            'has_membership_question'       => Membership::hasMembershipQuestion($this->resource),
            'is_rule_confirmation'          => $this->resource->is_rule_confirmation,
            'is_answer_membership_question' => $this->resource->is_answer_membership_question,
            'manages'                       => [
                'max_membership_questions'  => GroupFacade::getMaximumMembershipQuestion(),
                'maximum_number_group_rule' => GroupFacade::getMaximumNumberGroupRule(),
            ],
            'is_muted'       => $isMuted,
            'pending_invite' => $pendingInvite ? ResourceGate::asResource(
                $pendingInvite,
                'item',
                false
            ) : null,
            'profile_settings' => UserPrivacy::hasAccessProfileSettings($context, $this->resource),
            'privacy_detail'   => $privacyDetail,
            'changedPrivacy'   => $this->getChangePrivacy(),
            'cover_resource'   => $this->getCoverResources(),
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

    protected function getChangePrivacy(): ?array
    {
        $pendingPrivacy = $this->resource->getPendingPrivacy;

        if (null == $pendingPrivacy) {
            return null;
        }

        $currentPrivacyName = __p(PrivacyTypeHandler::PRIVACY_PHRASE[$this->resource->privacy_type]);
        $pendingPrivacyName = __p(PrivacyTypeHandler::PRIVACY_PHRASE[$pendingPrivacy->privacy_type]);

        return [
            'current_type' => $currentPrivacyName,
            'pending_type' => $pendingPrivacyName,
        ];
    }

    protected function getCoverResources(): ?JsonResource
    {
        return !empty($this->resource->cover)
            ? ResourceGate::asDetail($this->resource->cover()->first())
            : null;
    }
}

<?php

namespace MetaFox\User\Http\Resources\v1\User;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Core\Support\Facades\Language;
use MetaFox\Core\Support\Facades\Timezone;
use MetaFox\Platform\Facades\ResourceGate;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Support\Browse\Traits\User\ExtraTrait;
use MetaFox\User\Support\Facades\User as UserFacade;
use MetaFox\User\Support\Facades\UserBlocked;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Support\Facades\UserValue;
use MetaFox\User\Traits\FriendStatisticTrait;
use MetaFox\User\Traits\UserLocationTrait;
use MetaFox\User\Traits\UserStatisticTrait;

/**
 * Class UserDetail.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserDetail extends JsonResource
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
        $profile = $this->resource->profile;

        $context = user();

        $summary = null;
        if (UserPrivacy::hasAccess($context, $this->resource, 'profile.profile_info') === true) {
            $summary = UserFacade::getSummary($context, $this->resource);
        }

        $birthdaySetting = UserValue::getUserValueSettingByName($this->resource, 'user_profile_date_of_birth_format');
        $activityPoint   = UserValue::getUserValueSettingByName($context, 'total_activity_points') ?? 0;
        $languageId      = $profile->language_id;

        $custom = $this->resource->customProfile();

        $bio = parse_output()->parseUrl($custom['bio'] ?? null);
        // $aboutMe = parse_output()->parseUrl($custom['about_me'] ?? null); @todo: Using this?
        $data = [
            'id'                    => $this->resource->entityId(),
            'module_name'           => $this->resource->entityType(),
            'resource_name'         => $this->resource->entityType(),
            'full_name'             => $this->resource->full_name,
            'user_name'             => $this->resource->user_name,
            'avatar'                => $profile->avatars,
            'avatar_id'             => $profile->avatar_id,
            'last_name'             => UserFacade::getLastName($this->resource->full_name),
            'first_name'            => UserFacade::getFirstName($this->resource->full_name),
            'gender'                => $profile->gender,
            'language_id'           => $languageId,
            'language_name'         => $languageId ? Language::getName($languageId) : '',
            'joined'                => $this->resource->created_at, // formatted to ISO-8601 as v4
            'time_zone'             => Timezone::getName($profile->timezone_id),
            'default_currency'      => $profile->currency_id,
            'cover'                 => $profile->covers,
            'cover_photo_id'        => $profile->getCoverId(),
            'cover_photo_position'  => $profile->cover_photo_position,
            'profile_settings'      => UserPrivacy::hasAccessProfileSettings($context, $this->resource),
            'profile_menu_settings' => UserPrivacy::hasAccessProfileMenuSettings($context, $this->resource),
            'post_types'            => [],
            'summary'               => $summary,
            'activity_total'        => 0,
            'activity_points'       => $activityPoint,
            'is_featured'           => $this->resource->is_featured,
            'age'                   => UserFacade::getAge($this->resource->profile->birthday),
            'is_blocked'            => $this->isBlocked(),
            'short_name'            => UserFacade::getShortName($this->resource->full_name),
            'creation_date'         => $this->resource->created_at,
            'modification_date'     => $this->resource->updated_at,
            'link'                  => $this->resource->toLink(),
            'url'                   => $this->resource->toUrl(),
            'statistic'             => $this->getStatistic(),
            'friend_statistic'      => $this->getFriendStatistic(),
            'extra'                 => $this->getExtra(),
            'friends'               => [],
            'privacy'               => 0,
            'is_owner'              => $profile->isOwner($context),
            'status_id'             => 0,
            'message'               => '',
            'friendship'            => UserFacade::getFriendship($context, $this->resource),
            'is_following'          => UserFacade::isFollowing($context, $this->resource),
            'bio'                   => $bio,
            'interest'              => $custom['interest'] ?? null,
            'about_me'              => $custom['about_me'] ?? null,
            'hobbies'               => $custom['hobbies'] ?? null,
            'address'               => UserFacade::getAddress($context, $this->resource),
            'birthday_text'         => UserFacade::getBirthday($profile->birthday, $birthdaySetting),
            'gender_text'           => UserFacade::getGender($profile),
            'relationship_text'     => $profile->relationship_text,
            'is_deleted'            => $this->resource->isDeleted(),
            'cover_resource'        => $this->getCoverResources(),
            'avatar_resource'       => $this->getAvatarResources(),
        ];

        $data = array_merge($data, $this->getLocation($context, $this->resource));

        return $data;
    }

    /**
     * @return bool
     * @throws AuthenticationException
     */
    protected function isBlocked(): bool
    {
        return UserBlocked::isBlocked($this->resource, user());
    }

    protected function getCoverResources(): ?JsonResource
    {
        $profile = $this->resource->profile;

        return !empty($profile->cover)
            ? ResourceGate::asDetail($profile->cover()->first())
            : null;
    }

    protected function getAvatarResources(): ?JsonResource
    {
        $profile = $this->resource->profile;

        return !empty($profile->avatar)
            ? ResourceGate::asDetail($profile->avatar()->first())
            : null;
    }
}

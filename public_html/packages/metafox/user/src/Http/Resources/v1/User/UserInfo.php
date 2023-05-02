<?php

namespace MetaFox\User\Http\Resources\v1\User;

use ArrayObject;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use MetaFox\Profile\Repositories\ProfileRepositoryInterface;
use MetaFox\User\Models\User as Model;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Support\Facades\User;
use MetaFox\User\Support\Facades\UserPrivacy;
use MetaFox\User\Support\Facades\UserValue;
use MetaFox\User\Support\User as Support;
use MetaFox\User\Traits\UserLocationTrait;

/**
 * |--------------------------------------------------------------------------
 * | Resource
 * |--------------------------------------------------------------------------
 * | stub: /packages/resources/base.stub
 * | @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview.
 **/

/**
 * Class User.
 * @property Model $resource
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class UserInfo extends JsonResource
{
    use UserLocationTrait;

    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     *
     * @return array<string,           mixed>
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function toArray($request): array
    {
        $profile = $this->resource->profile;
        $custom  = $this->resource->customProfile();

        $profileSettings = $this->getProfileSettings();
        $data            = [
            'id'            => $this->resource->entityId(),
            'module_name'   => 'user',
            'resource_name' => $this->resource->entityType(),
        ];

        $sections = new ArrayObject([]);

        if ($profileSettings['profile_basic_info']) {
            $sections['basic_info'] = [
                'component' => 'layout.section.icon_list',
                'label'     => __p('user::phrase.basic_information'),
                'fields'    => $this->getBasicInfo($profile),
            ];
        }

        resolve(ProfileRepositoryInterface::class)->viewSections($this->resource, $sections);

        $data['sections'] = $sections;

        return $data;
    }

    /**
     * @param  UserProfile          $profile
     * @return array<string, mixed>
     */
    protected function getBasicInfo(UserProfile $profile): array
    {
        $context         = user();
        $profileSettings = $this->getProfileSettings();
        $birthdaySetting = UserValue::getUserValueSettingByName($this->resource, 'user_profile_date_of_birth_format');

        $locationValue = $this->getLocationValue($context, $this->resource);

        $birthday = '';

        if ($birthdaySetting != Support::DATE_OF_BIRTH_DONT_SHOW) {
            $birthday = User::getBirthday($profile->birthday, $birthdaySetting);
        }
        $data = [
            'relationship' => [
                'icon'  => 'ico-heart-o',
                'label' => __p('user::phrase.relationship_status'),
                'value' => $profile->relationship_text,
            ],
            'gender' => [
                'icon'  => 'ico-sex-unknown',
                'label' => __p('user::phrase.gender'),
                'value' => User::getGender($profile),
            ],
            'birthdate' => [
                'icon'  => 'ico-birthday-cake',
                'label' => __p('user::phrase.birth_date'),
                'value' => $birthday,
            ],
            'location' => [
                'icon'  => 'ico-checkin-o',
                'label' => __p('user::phrase.location'),
                'value' => $locationValue,
            ],
            'address' => [
                'icon'  => 'ico-checkin-o',
                'label' => __p('user::phrase.address'),
                'value' => $custom['address'] ?? null,
            ],
            'member_since' => [
                'label'  => __p('user::phrase.member_since'),
                'value'  => '',
                'type'   => 'datetime',
                'format' => 'DD/MM/YYYY',
                'icon'   => 'ico-user3-two',
            ],
            'membership' => [
                'icon'  => 'ico-calendar-o',
                'label' => __p('user::phrase.membership'),
                'value' => $this->resource->roles->pluck('name'),
            ],
        ];

        if ($profileSettings['profile_view_location']) {
            $data['location'] = [
                'icon'  => 'ico-checkin-o',
                'label' => __p('user::phrase.location'),
                'value' => $locationValue,
            ];
        }

        return $data;
    }

    protected function getProfileSettings()
    {
        $context = user();

        return UserPrivacy::hasAccessProfileSettings($context, $this->resource);
    }
}

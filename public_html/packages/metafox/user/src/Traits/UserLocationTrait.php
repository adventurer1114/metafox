<?php

namespace MetaFox\User\Traits;

use Illuminate\Support\Facades\Gate;
use MetaFox\Core\Support\Facades\Country;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\User as UserContract;
use MetaFox\User\Models\User;
use MetaFox\User\Policies\UserPolicy;

trait UserLocationTrait
{
    /**
     * @return array<string, mixed>
     */
    public function getLocation(UserContract $context, User $resource): array
    {
        $locationData = [
            'country_iso'        => null,
            'city_location'      => null,
            'postal_code'        => null,
            'country_city_code'  => null,
            'country_state_id'   => null,
            'country_name'       => null,
            'country_state_name' => null,
        ];

        if ($this->canViewLocation($context, $resource)) {
            $profile = $resource->profile;

            $locationData = [
                'country_iso'        => $profile->country_iso,
                'city_location'      => $profile->city_location,
                'postal_code'        => $profile->postal_code,
                'country_city_code'  => $profile->country_city_code,
                'country_state_id'   => $profile->country_state_id,
                'country_name'       => Country::getCountryName($profile->country_iso),
                'country_state_name' => Country::getCountryStateName($profile->country_iso, $profile->country_state_id),
            ];
        }

        return $locationData;
    }

    public function canViewLocation(UserContract $context, UserContract $user): bool
    {
        return policy_check(UserPolicy::class, 'viewLocation', $context, $user);
    }

    public function getLocationValue(UserContract $context, $resource): string
    {
        if (!$resource instanceof HasUserProfile) {
            return '';
        }

        $locationData = $this->getLocation($context, $resource);
        $state        = empty($locationData['country_state_name']);
        $country      = !empty($locationData['country_name']);

        if (!empty($locationData['city_location'])) {
            return $locationData['city_location'];
        }

        if (!$country) {
            return '';
        }

        return match ($state) {
            true    => $locationData['country_name'],
            default => $locationData['country_state_name'] . ', ' . $locationData['country_name'],
        };
    }
}

<?php

namespace MetaFox\User\Support;

use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as SystemRequest;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as CollectionSupport;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use MetaFox\Authorization\Models\Role;
use MetaFox\Core\Support\Facades\Country;
use MetaFox\Localize\Repositories\TimezoneRepositoryInterface;
use MetaFox\Platform\Contracts\HasUserProfile;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\MetaFoxConstant;
use MetaFox\Platform\UserRole;
use MetaFox\User\Contracts\UserContract;
use MetaFox\User\Models\User as UserModel;
use MetaFox\User\Models\UserActivity;
use MetaFox\User\Models\UserGender;
use MetaFox\User\Models\UserProfile;
use MetaFox\User\Repositories\Contracts\UserRepositoryInterface;
use MetaFox\User\Repositories\UserRelationRepositoryInterface;
use MetaFox\User\Traits\UserLocationTrait;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class User implements UserContract
{
    // todo DO NOT MIX TRAIT HERE.
    use UserLocationTrait;

    public const MENTION_REGEX                 = '^\[user=(.*?)\]^';
    public const DATE_OF_BIRTH_DONT_SHOW       = 1;
    public const DATE_OF_BIRTH_SHOW_DAY_MONTH  = 2;
    public const DATE_OF_BIRTH_SHOW_AGE        = 3;
    public const DATE_OF_BIRTH_SHOW_ALL        = 4;
    public const AUTO_APPROVED_TAGGER_POST     = 0;
    public const NOT_AUTO_APPROVED_TAGGER_POST = 1;
    public const AUTO_APPROVED_TAGGED_SETTING  = 'user_auto_add_tagger_post';

    private UserRepositoryInterface $repository;

    public function __construct(
        UserRepositoryInterface $repository,
        protected UserRelationRepositoryInterface $relationRepository
    ) {
        $this->repository = $repository;
    }

    public function isBan(int $userId): bool
    {
        return $this->repository->isBanned($userId);
    }

    public function getFriendship(ContractUser $user, ContractUser $targetUser): ?int
    {
        return app('events')->dispatch('friend.get_friend_ship', [$user, $targetUser], true);
    }

    public function getGuestUser(): Authenticatable
    {
        $user            = new UserModel();
        $user->id        = MetaFoxConstant::GUEST_USER_ID;
        $user->user_name = 'guest';
        $user->full_name = 'Guest';

        try {
            // setup wizard issues auth_roles still not exists.
            $guestUser    = Role::findById(UserRole::GUEST_USER);
            $guestProfile = new UserProfile(['id' => $user->id]);
            $user->setRelation('roles', new Collection([$guestUser]));
            $user->setRelation('profile', $guestProfile);
        } catch (Exception) {
        }

        return $user;
    }

    public function getGender(UserProfile $profile): ?string
    {
        if ($profile->gender instanceof UserGender) {
            return $profile->gender->name;
        }

        return null;
    }

    public function getBirthday(?string $birthday, ?int $formatValue = null): ?string
    {
        if (!is_string($birthday)) {
            return null;
        }

        $time = Carbon::createFromFormat('Y-m-d', $birthday);

        if ($time === false) {
            return $birthday;
        }

        return match ($formatValue) {
            self::DATE_OF_BIRTH_SHOW_DAY_MONTH => $time->format(Settings::get('user.user_dob_month_day', 'F j')),
            self::DATE_OF_BIRTH_SHOW_AGE       => trans_choice(
                'user::trans_choice.years_old',
                $time->age,
                ['year' => $time->age]
            ),
            self::DATE_OF_BIRTH_SHOW_ALL => $time->format(Settings::get('user.user_dob_month_day_year', 'F j, Y')),
            default                      => null,
        };
    }

    public function getUserAge(?string $birthday): ?int
    {
        if (!is_string($birthday)) {
            return null;
        }

        $time = Carbon::createFromFormat('Y-m-d', $birthday);

        if ($time === false) {
            return null;
        }

        return $time->age;
    }

    public function getFullBirthdayFormat(): array
    {
        return [
            'F j, Y', 'Y-m-d', 'm/d/Y', 'd/m/Y',
        ];
    }

    public function getMonthDayBirthdayFormat(): array
    {
        return [
            'F j', 'm-d', 'm/d', 'd/m',
        ];
    }

    public function splitName(string $name): array
    {
        // @todo need to test after.
        $firstName = $middleName = $lastName = '';

        $parts = [];
        while (strlen(trim($name)) > 0) {
            $name = trim($name);
            /** @var string $string */
            $string  = preg_replace('#.*\s([\w-]*)$#', '$1', $name);
            $parts[] = $string;

            /** @var string $tryName */
            $tryName = preg_replace('#' . preg_quote($string, '#') . '#', '', $name);
            $name    = trim($tryName);
        }

        if (!empty($parts)) {
            $parts      = array_reverse($parts);
            $firstName  = $parts[0];
            $middleName = (isset($parts[2])) ? $parts[1] : '';
            $lastName   = (isset($parts[2])) ? $parts[2] : ($parts[1] ?? '');
        }

        return [$lastName, $firstName, $middleName];
    }

    public function getLastName(string $name): string
    {
        [$lastName] = $this->splitName($name);

        return $lastName;
    }

    public function getFirstName(string $name): string
    {
        [, $firstName, $middleName] = $this->splitName($name);

        if ($middleName && $middleName != '') {
            return $firstName . ' ' . $middleName;
        }

        return $firstName;
    }

    public function getShortName(string $name): string
    {
        $lastName  = self::getLastName($name);
        $firstName = self::getFirstName($name);

        $lastNameString  = ((isset($lastName[0])) ? $lastName[0] : '');
        $firstNameString = ((isset($firstName[0])) ? $firstName[0] : '');

        if (!$lastNameString) {
            return Str::upper($firstNameString . ((isset($firstName[1])) ? $firstName[1] : ''));
        }

        $shortName = $firstNameString . $lastNameString;

        return Str::upper($shortName);
    }

    /**
     * @inherhitDoc
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function getSummary(ContractUser $context, ContractUser $user): ?string
    {
        $summary = [];
        $profile = $user->profile;

        if (!$profile instanceof UserProfile) {
            return null;
        }
        $address = $this->getAddress($context, $user);
        if ($address) {
            $summary[] = $address;
        }

        $summary[] = Str::limit($profile->about_me, 155);

        return implode('. ', $summary);
    }

    public function getAge(?string $birthday): ?int
    {
        $age = null;

        try {
            if (!empty($birthday)) {
                $age = Carbon::parse($birthday)->age;
            }
        } catch (Exception $e) {
            // Just silent.
        }

        return $age;
    }

    public function getNewAgePhrase(?string $birthday): ?string
    {
        $newAgePhrase = null;
        $age          = $this->getAge($birthday);

        if ($age) {
            $newAge = $age + 1;
            if (Carbon::parse($birthday)->format('m-d') == Carbon::now()->format('m-d')) {
                $newAge = $age;
            }
            $newAgePhrase = __p('user::phrase.years_old', ['year' => $newAge]);
        }

        return $newAgePhrase;
    }

    public function getTimeZoneForForm(): array
    {
        $timezones     = [];
        $timezonesData = resolve(TimezoneRepositoryInterface::class)->getTimeZones();

        if ($timezonesData) {
            $timezones = collect($timezonesData[0])->map(function ($value) {
                return [
                    'id'   => $value['id'],
                    'name' => "{$value['name']} ({$value['diff_from_gtm']})",
                ];
            })->toArray();
        }

        return $timezones;
    }

    public function getTimeZoneNameById(int $id): ?string
    {
        if (0 == $id) {
            return null;
        }

        $timezonesData = resolve(TimezoneRepositoryInterface::class)->getTimeZones();

        if ($timezonesData) {
            if (isset($timezonesData[0][$id])) {
                return $timezonesData[0][$id]['name'];
            }
        }

        return null;
    }

    public function getUsersByRoleId(int $roleId): ?CollectionSupport
    {
//        $this->repository->
        return $this->repository->getUsersByRoleId($roleId);
    }

    public function getMentions(string $content): array
    {
        $userIds = [];
        try {
            preg_match_all(self::MENTION_REGEX, $content, $matches);
            $userIds = array_unique($matches[1]);
        } catch (Exception $e) {
            // Silent.
        }

        return $userIds;
    }

    public function getPossessiveGender(?UserGender $gender): string
    {
        $defaultGender = __p('core::phrase.their');

        if (null === $gender) {
            return $defaultGender;
        }

        switch ($gender->entityId()) {
            case MetaFoxConstant::GENDER_MALE:
                $gender = __p('core::phrase.his');
                break;
            case MetaFoxConstant::GENDER_FEMALE:
                $gender = __p('core::phrase.her');
                break;
            default:
                $gender = $defaultGender;
        }

        return $gender;
    }

    public function updateLastLogin(?ContractUser $context): bool
    {
        // check login as guest.
        if (!$context) {
            return false;
        }

        if ($context instanceof HasUserProfile && !$context->isGuest()) {
            return UserActivity::query()
                ->where('id', $context->entityId())
                ->update([
                    'last_login'      => now(),
                    'last_ip_address' => Request::ip(),
                ]);
        }

        return false;
    }

    public function updateLastActivity(ContractUser $context): bool
    {
        if ($context instanceof HasUserProfile && $context->id) {
            return UserActivity::query()
                ->where('id', $context->id)
                ->where('last_activity', '<=', Carbon::now()->subMinutes(5))
                ->update([
                    'last_activity' => now(),
                ]);
        }

        return false;
    }

    public function updateInvisibleMode(ContractUser $context, int $isInvisible): UserModel
    {
        return $this->repository->updateUser($context, $context->entityId(), ['is_invisible' => $isInvisible]);
    }

    /**
     * @param ContractUser $user
     *
     * @return array<int, mixed>
     */
    public function getNotificationSettingsByChannel(ContractUser $user, string $channel): array
    {
        $settings = app('events')
            ->dispatch('notification.get_notification_settings_by_channel', [$user, $channel], true);

        return !empty($settings) ? $settings : [];
    }

    /**
     * @param ContractUser      $context
     * @param array<string,int> $attributes
     *
     * @return bool
     */
    public function updateNotificationSettingsByChannel(ContractUser $context, array $attributes): bool
    {
        return app_active('metafox/notification')
            && app('events')->dispatch(
                'notification.update_email_notification_settings',
                [$context, $attributes],
                true
            );
    }

    public function hasPendingSubscription(SystemRequest $request, ContractUser $user, bool $isMobile = false): ?array
    {
        $segments = $request->segments();

        $response = null;

        if (count($segments) > 2) {
            $segments = array_slice($segments, 2);

            $firstSegment = array_shift($segments);

            $allowedEndpoints = $this->getAllowedEndpointsForPendingSubscription();

            if (!preg_match('/^subscription(-[a-z]+)?$/', $firstSegment) && !in_array(
                $firstSegment,
                $allowedEndpoints
            )) {
                $response = app('events')->dispatch('subscription.invoice.has_pending', [$user, $isMobile], true);
            }
        }

        return $response;
    }

    protected function getAllowedEndpointsForPendingSubscription(): array
    {
        $allowedEndpoints = ['me', 'core', 'seo', 'chat-room'];

        $extraEndpoints = app('events')->dispatch('user.pending_subscription.allow_endpoints');

        if (!is_array($extraEndpoints)) {
            return $allowedEndpoints;
        }

        foreach ($extraEndpoints as $extraEndpoint) {
            if (!is_array($extraEndpoint)) {
                continue;
            }

            $allowedEndpoints = array_merge($allowedEndpoints, $extraEndpoint);
        }

        return array_unique($allowedEndpoints);
    }

    /**
     * @inheritDoc
     */
    public function getAddress(ContractUser $context, ContractUser $user): ?string
    {
        if (!$this->canViewLocation($context, $user)) {
            return null;
        }

        if (!$user->profile instanceof UserProfile) {
            return null;
        }

        $profile = $user->profile;
        $country = $state = '';
        $city    = $profile->city_location ?? '';
        if ($profile->country_iso) {
            $country = Country::getCountryName($profile->country_iso);
        }

        if ($country && $profile->country_state_id) {
            $state = Country::getCountryStateName($profile->country_iso, $profile->country_state_id);
        }
        $locations = array_filter([$city, $state, $country]);

        return $locations ? 'Lives in ' . implode(', ', $locations) : null;
    }

    /**
     * @inheritDoc
     */
    public function isFollowing(ContractUser $context, ContractUser $user): bool
    {
        if (!app('events')->dispatch('follow.is_follow', [$context, $user], true)) {
            return false;
        }

        return true;
    }
}

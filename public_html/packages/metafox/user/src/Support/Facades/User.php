<?php

namespace MetaFox\User\Support\Facades;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request as SystemRequest;
use Illuminate\Support\Facades\Facade;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\User\Contracts\UserContract;
use MetaFox\User\Models\User as ModelUser;
use MetaFox\User\Models\UserGender;
use MetaFox\User\Models\UserProfile;

/**
 * Class User.
 *
 * @see \MetaFox\User\Support\User
 * @method static bool                 isFollowing(ContractUser $context, ContractUser $user)
 * @method static bool                 isBan(int $userId)
 * @method static int                  getFriendship(ContractUser $user, ContractUser $targetUser)
 * @method static string|null          getGender(UserProfile $profile)
 * @method static string|null          getBirthday(?string $birthday, ?int $format = null)
 * @method static string               getLastName(string $name)
 * @method static string               getFirstName(string $name)
 * @method static string               getShortName(string $name)
 * @method static string               getSummary(ContractUser $context, ContractUser $user)
 * @method static string               getAddress(ContractUser $context, ContractUser $user)
 * @method static string               getAge(?string $birthday)
 * @method static string               getNewAgePhrase(?string $birthday)
 * @method static Authenticatable      getGuestUser()
 * @method static array                getTimeZoneForForm()
 * @method static string|null          getTimeZoneNameById(int $id)
 * @method static Builder[]|Collection getUsersByRoleId(int $roleId)
 * @method static int[]                getMentions(string $content)
 * @method static string               getPossessiveGender(?UserGender $gender)
 * @method static bool                 updateLastLogin(ContractUser $context)
 * @method static bool                 updateLastActivity(ContractUser $context)
 * @method static ModelUser            updateInvisibleMode(ContractUser $context, int $isInvisible)
 * @method static array                getNotificationSettingsByChannel(ContractUser $context, string $channel)
 * @method static array                getFullBirthdayFormat()
 * @method static array                getMonthDayBirthdayFormat()
 * @method static bool                 updateNotificationSettingsByChannel(ContractUser $context, array $attributes)
 * @method static array                hasPendingSubscription(SystemRequest $request, ContractUser $user, bool $isMobile)
 * @method static int|null             getUserAge(?string $birthday)
 *                                                                                                                        =
 *                                                                                                                        false)
 */
class User extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return UserContract::class;
    }
}

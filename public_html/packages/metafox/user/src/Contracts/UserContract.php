<?php

namespace MetaFox\User\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request as SystemRequest;
use Illuminate\Support\Collection as CollectionSupport;
use MetaFox\Platform\Contracts\User as ContractUser;
use MetaFox\User\Models\UserGender;
use MetaFox\User\Models\UserProfile;

/**
 * Interface UserContract.
 */
interface UserContract
{
    /**
     * @param int $userId
     *
     * @return bool
     */
    public function isBan(int $userId): bool;

    /**
     * @param ContractUser $user
     * @param ContractUser $targetUser
     *
     * @return int|null
     */
    public function getFriendship(ContractUser $user, ContractUser $targetUser): ?int;

    /**
     * @return Authenticatable
     */
    public function getGuestUser(): Authenticatable;

    /**
     * @param UserProfile $profile
     *
     * @return string|null
     */
    public function getGender(UserProfile $profile): ?string;

    /**
     * @param  string|null $birthday
     * @param  int|null    $formatValue
     * @return string|null
     */
    public function getBirthday(?string $birthday, ?int $formatValue = null): ?string;

    /**
     * @param  string|null $birthday
     * @return int|null
     */
    public function getUserAge(?string $birthday): ?int;

    /**
     * @param string $name
     *
     * @return array<mixed>
     */
    public function splitName(string $name): array;

    /**
     * @param string $name
     *
     * @return string
     */
    public function getLastName(string $name): string;

    /**
     * @param string $name
     *
     * @return string
     */
    public function getFirstName(string $name): string;

    /**
     * @param string $name
     *
     * @return string
     */
    public function getShortName(string $name): string;

    /**
     * @param ContractUser $context
     * @param ContractUser $user
     *
     * @return string|null
     */
    public function getSummary(ContractUser $context, ContractUser $user): ?string;

    /**
     * @param string|null $birthday
     *
     * @return int|null
     */
    public function getAge(?string $birthday): ?int;

    /**
     * @param  UserGender|null $gender
     * @return string
     */
    public function getPossessiveGender(?UserGender $gender): string;

    /**
     * @param string|null $birthday
     *
     * @return string|null
     */
    public function getNewAgePhrase(?string $birthday): ?string;

    /**
     * @return array<int, mixed>
     */
    public function getTimeZoneForForm(): array;

    /**
     * @param int $id
     *
     * @return string|null
     */
    public function getTimeZoneNameById(int $id): ?string;

    /**
     * @param  int                    $roleId
     * @return CollectionSupport|null
     */
    public function getUsersByRoleId(int $roleId): ?CollectionSupport;

    /**
     * @param string $content
     *
     * @return int[]
     */
    public function getMentions(string $content): array;

    /**
     * @param  ContractUser $user
     * @param  string       $channel
     * @return mixed
     */
    public function getNotificationSettingsByChannel(ContractUser $user, string $channel);

    /**
     * @param  ContractUser $context
     * @param  array        $attributes
     * @return mixed
     */
    public function updateNotificationSettingsByChannel(ContractUser $context, array $attributes);

    /**
     * @return array
     */
    public function getFullBirthdayFormat(): array;

    /**
     * @return array
     */
    public function getMonthDayBirthdayFormat(): array;

    /**
     * @param  SystemRequest $request
     * @param  ContractUser  $user
     * @param  bool          $isMobile
     * @return array|null
     */
    public function hasPendingSubscription(SystemRequest $request, ContractUser $user, bool $isMobile = false): ?array;

    /**
     * @param  ContractUser $context
     * @param  ContractUser $user
     * @return string|null
     */
    public function getAddress(ContractUser $context, ContractUser $user): ?string;

    /**
     * @param  ContractUser $context
     * @param  ContractUser $user
     * @return bool
     */
    public function isFollowing(ContractUser $context, ContractUser $user): bool;
}

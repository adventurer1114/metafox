<?php

namespace MetaFox\Activity\Contracts;

use MetaFox\Platform\Contracts\User;

/**
 * Interface ActivitySnoozeManager
 * @package MetaFox\Activity\Contracts
 */
interface ActivitySnoozeManager
{
    public function getCacheName(int $userId): string;

    public function clearCache(int $userId): void;

    public function isSnooze(User $user, ?User $owner = null): bool;

    public function isHideAll(User $user, ?User $owner = null): bool;

    /**
     * Get hidden users of user.
     *
     * @param User $user
     *
     * @return array<int, array<string, mixed>>
     */
    public function getSnoozedUsers(User $user): array;
}

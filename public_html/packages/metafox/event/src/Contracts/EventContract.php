<?php

namespace MetaFox\Event\Contracts;

use MetaFox\Platform\Contracts\User;

interface EventContract
{
    /**
     * @return array
     */
    public function getPrivacyList(): array;

    /**
     * @param  User      $user
     * @param  User      $owner
     * @return bool|null
     */
    public function checkFeedReactingPermission(User $user, User $owner): ?bool;

    /**
     * @param  User $user
     * @param  int  $eventId
     * @return bool
     */
    public function checkPermissionMassEmail(User $user, int $eventId): bool;

    /**
     * @param  string $locationName
     * @return ?array
     */
    public function createLocationWithName(string $locationName): ?array;
}

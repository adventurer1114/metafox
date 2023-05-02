<?php

namespace MetaFox\Activity\Support;

use Exception;
use MetaFox\Activity\Models\Subscription;

class ActivitySubscription
{
    /**
     * @param  int          $userId
     * @param  int          $ownerId
     * @param  bool         $active
     * @param  string|null  $specialType
     * @return Subscription
     */
    public function addSubscription(int $userId, int $ownerId, bool $active = true, ?string $specialType = null): Subscription
    {
        $data = [
            'user_id'      => $userId,
            'owner_id'     => $ownerId,
            'is_active'    => $active,
            'special_type' => $specialType,
        ];

        return Subscription::query()->firstOrCreate($data);
    }

    /**
     * @param  int         $userId
     * @param  int         $ownerId
     * @param  string|null $specialType
     * @return bool
     */
    public function deleteSubscription(int $userId, int $ownerId, ?string $specialType = null): bool
    {
        $subscription = $this->getSubscription($userId, $ownerId, $specialType);

        if (!$subscription instanceof Subscription) {
            return true;
        }

        return (bool) $subscription->delete();
    }

    /**
     * @param int  $userId
     * @param int  $ownerId
     * @param bool $active
     *
     * @return false|Subscription
     * @SuppressWarnings(PHPMD.BooleanArgumentFlag)
     */
    public function updateSubscription(int $userId, int $ownerId, bool $active = false, ?string $specialType = null)
    {
        $subscription = $this->getSubscription($userId, $ownerId, $specialType);

        if (!$subscription instanceof Subscription) {
            return $this->addSubscription($userId, $ownerId, $active, $specialType);
        }

        $subscription->is_active = $active;

        return $subscription->save() ? $subscription : false;
    }

    /**
     * @param int $userId
     * @param int $ownerId
     *
     * @return Subscription|null
     */
    public function getSubscription(int $userId, int $ownerId, ?string $specialType = null): ?Subscription
    {
        $subscription = Subscription::query()
            ->where([
                'user_id'      => $userId,
                'owner_id'     => $ownerId,
                'special_type' => $specialType,
            ])
            ->first();

        if (!$subscription instanceof Subscription) {
            return null;
        }

        return $subscription;
    }
}

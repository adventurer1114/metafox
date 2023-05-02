<?php

namespace MetaFox\Activity\Support;

use Illuminate\Database\Eloquent\Collection;
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
    public function addSubscription(
        int $userId,
        int $ownerId,
        bool $active = true,
        ?string $specialType = null
    ): Subscription {
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
     * @param  int                $userId
     * @param  int                $ownerId
     * @param  bool               $active
     * @param  string|null        $specialType
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
     * @param  int               $userId
     * @param  int               $ownerId
     * @param  string|null       $specialType
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

    public function isExist(int $userId, int $ownerId): bool
    {
        return Subscription::query()
            ->where([
                'user_id'      => $userId,
                'owner_id'     => $ownerId,
                'is_active'    => true,
                'special_type' => null,
            ])
            ->exists();
    }

    public function getSubscriptions(array $attributes): Collection
    {
        return Subscription::query()
            ->where($attributes)
            ->get();
    }

    public function buildSubscriptions(array $attributes)
    {
        return Subscription::query()
            ->where($attributes);
    }
}

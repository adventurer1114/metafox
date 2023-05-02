<?php

namespace MetaFox\Activity\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use MetaFox\Activity\Contracts\ActivitySnoozeManager as Contract;
use MetaFox\Activity\Models\Snooze as Model;
use MetaFox\Platform\Contracts\User;

class ActivitySnoozeManager implements Contract
{
    /** @var array<int, array<string, mixed>> */
    private $snoozedList = [];

    public function getCacheName(int $userId): string
    {
        return sprintf(CacheManager::ACTIVITY_SNOOZE_CACHE, $userId);
    }

    public function clearCache(int $userId): void
    {
        $cacheName = $this->getCacheName($userId);
        Cache::forget($cacheName);
        if (isset($this->snoozedList[$userId])) {
            unset($this->snoozedList[$userId]);
        }
    }

    public function isSnooze(User $user, ?User $owner = null): bool
    {
        if (!$owner instanceof User) {
            return false;
        }

        if ($user->entityId() == $owner->entityId()) {
            return false;
        }

        $snoozedUsers = $this->getSnoozedUsers($user);

        if (!isset($snoozedUsers[$owner->entityId()])) {
            return false;
        }

        return true;
    }

    public function isHideAll(User $user, ?User $owner = null): bool
    {
        if (!$owner instanceof User) {
            return false;
        }

        if ($user->entityId() == $owner->entityId()) {
            return false;
        }

        $snoozedUsers = $this->getSnoozedUsers($user);

        if (!array_key_exists($owner->entityId(), $snoozedUsers)) {
            return false;
        }

        $hiddenRecord = $snoozedUsers[$owner->entityId()];

        return array_key_exists('is_snooze_forever', $hiddenRecord) && $hiddenRecord['is_snooze_forever'] === 1;
    }

    public function getSnoozedUsers(User $user): array
    {
        if (!array_key_exists($user->entityId(), $this->snoozedList)) {
            $this->snoozedList[$user->entityId()] = Cache::remember(
                $this->getCacheName($user->entityId()),
                CacheManager::ACTIVITY_SNOOZE_CACHE_TIME,
                function () use ($user) {
                    return Model::query()
                        ->where('user_id', $user->entityId())
                        ->where(function (Builder $q) {
                            $q->where(function (Builder $q1) {
                                $q1->where('is_snooze_forever', '=', 0);
                                $q1->whereDate('snooze_until', '>', Carbon::now()->format('Y-m-d H:i:s'));
                            });
                            $q->orWhere('is_snooze_forever', '=', 1);
                        })
                        ->get([
                            'id', 'owner_id', 'is_snooze_forever', 'snooze_until', 'is_snoozed',
                        ])
                        ->keyBy('owner_id')
                        ->toArray();
                }
            );
        }

        return $this->snoozedList[$user->entityId()];
    }
}

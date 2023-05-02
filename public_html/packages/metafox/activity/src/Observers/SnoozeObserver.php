<?php

namespace MetaFox\Activity\Observers;

use Illuminate\Support\Carbon;
use MetaFox\Activity\Contracts\ActivitySnoozeManager;
use MetaFox\Activity\Models\Snooze;
use MetaFox\Activity\Support\Facades\ActivitySubscription;
use MetaFox\Activity\Support\Support;

class SnoozeObserver
{
    /** @var ActivitySnoozeManager */
    private $snoozeManager;

    public function __construct(ActivitySnoozeManager $snoozeManager)
    {
        $this->snoozeManager = $snoozeManager;
    }

    public function created(Snooze $model): void
    {
        if ($model->is_snoozed) {
            ActivitySubscription::updateSubscription($model->userId(), $model->ownerId(), false);

            $this->updateGlobalSubscription($model, false);
        }

        $this->snoozeManager->clearCache($model->userId());
    }

    public function updated(Snooze $model): void
    {
        $active = true;

        if ($model->is_snoozed && $model->snooze_until >= Carbon::now()) {
            $active = false;
        }

        ActivitySubscription::updateSubscription($model->userId(), $model->ownerId(), $active);

        $this->updateGlobalSubscription($model, $active);

        $this->snoozeManager->clearCache($model->userId());
    }

    public function deleted(Snooze $model): void
    {
        ActivitySubscription::updateSubscription($model->userId(), $model->ownerId(), true);

        $this->updateGlobalSubscription($model, true);

        $this->snoozeManager->clearCache($model->userId());
    }

    protected function updateGlobalSubscription(Snooze $model, bool $isActive): void
    {
        if (null === $model->owner) {
            return;
        }

        if (!$model->owner->hasSuperAdminRole()) {
            return;
        }

        ActivitySubscription::updateSubscription($model->userId(), $model->ownerId(), $isActive, Support::ACTIVITY_SUBSCRIPTION_VIEW_SUPER_ADMIN_FEED);
    }
}

<?php

namespace MetaFox\ActivityPoint\Listeners;

use MetaFox\ActivityPoint\Support\Facade\ActivityPoint;
use MetaFox\Platform\Contracts\User;

/**
 * Class PointUpdatedListener.
 * @ignore
 * @codeCoverageIgnore
 */
class PointUpdatedListener
{
    /**
     * @param User                 $context
     * @param User                 $owner
     * @param int                  $type
     * @param int                  $amount
     * @param array<string, mixed> $extra
     * @return void
     */
    public function handle(User $context, User $owner, int $type, int $amount, array $extra = []): void
    {
        // Update point statistic
        ActivityPoint::updateStatistic($context, $type, $amount);
    }
}

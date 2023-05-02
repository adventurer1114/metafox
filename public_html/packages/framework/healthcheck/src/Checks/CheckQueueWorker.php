<?php

namespace MetaFox\HealthCheck\Checks;

use Illuminate\Support\Carbon;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;

class CheckQueueWorker extends Checker
{
    protected ?string $cacheKey = 'health:checks:queue:latestHeartbeatAt';

    protected ?string $cacheStoreName = null;

    protected int $failWhenTestJobTakesLongerThanMinutes = 5;

    public function getCacheKey(?string $queue): string
    {
        $queue = $queue ?? 'default';
        return "{$this->cacheKey}.{$queue}";
    }

    public function check(): Result
    {
        $queues = ['default'];
        $result = $this->makeResult();

        $result->debug(__p('queue::phrase.default_label').': '.config('queue.default', 'database'));

        foreach ($queues as $queue) {
            $lastHeartbeatTimestamp = cache()->get($this->getCacheKey($queue));

            if (!$lastHeartbeatTimestamp) {
                $result->error("The `{$queue}` queue did not run yet.");
                continue;
            }

            $latestHeartbeatAt = Carbon::createFromTimestamp($lastHeartbeatTimestamp);

            $minutesAgo = $latestHeartbeatAt->diffInMinutes() + 1;

            if ($minutesAgo > $this->failWhenTestJobTakesLongerThanMinutes) {
                $result->error("The last run of the `{$queue}` queue was more than {$minutesAgo} minutes ago.");
                continue;
            }

            // pass
            $result->success("The last run of the `{$queue}` queue was {$minutesAgo} minutes ago.");
        }

        return $result;
    }

    public function getName()
    {
        return 'Queues';
    }
}
<?php

namespace MetaFox\HealthCheck\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use MetaFox\HealthCheck\Checks\CheckQueueWorker;

class CheckQueueWorkJob implements ShouldQueue
{
    use Queueable, Dispatchable;

    protected CheckQueueWorker $queueCheck;

    public function __construct(CheckQueueWorker $queueCheck)
    {
        $this->queueCheck = $queueCheck;
    }

    public function handle(): void
    {
        cache()->set(
            $this->queueCheck->getCacheKey($this->queue),
            now()->timestamp,
        );
    }
}
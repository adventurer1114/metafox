<?php

namespace MetaFox\HealthCheck\Checks;

use Illuminate\Support\Facades\Log;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;

class CheckLogging extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();

        $channels = [config('logging.default')];

        foreach ($channels as $channel) {
            try {
                Log::channel($channel)
                    ->debug('Checking if logs are writable - this message is logging by Health Check');

                $result->success(sprintf('Log channel "%s" is avaiable.', $channel));
            } catch (\Exception $exception) {
                $result->error(sprintf('Failed logging to channel "%s", exception: %s', $channel,
                    $exception->getMessage()));
            }
        }

        return $result;
    }

    public function getName()
    {
        return 'Logging';
    }
}
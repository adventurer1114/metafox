<?php

namespace MetaFox\HealthCheck\Checks;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;

class CheckCache extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();

        $result->success(sprintf('Using cache driver "%s"', config('cache.default')));
        $key = __METHOD__;

        $input = Str::random(5);

        Cache::set($key, $input);

        $output = Cache::get($key);

        if ($input !== $output) {
            $result->error(sprintf('Cache does not work property'));
        }

        return $result;
    }

    public function getName()
    {
        return 'Cache';
    }
}
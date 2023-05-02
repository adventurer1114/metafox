<?php

namespace MetaFox\HealthCheck\Checks;

use Illuminate\Support\Facades\Http;
use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;

class CheckReachableUrls extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();
        $urls = [
             'https://api.facebook.com',
            // 'https://cloudcall-s01.phpfox.com/build-service/ping'
        ];

        foreach ($urls as $url) {
            try {
                $response = Http::timeout(10)
                    ->retry(2)
                    ->get($url);

                if ($response->successful()) {
                    $result->success(sprintf("Reached %s", $url));
                }
            } catch (\Exception $exception) {
                $result->error(sprintf('Failed pinging %s, exception: %s', $url, $exception->getMessage()));
            }
        }

        return $result;
    }

    public function getName()
    {
        return 'Reachable Urls';
    }
}
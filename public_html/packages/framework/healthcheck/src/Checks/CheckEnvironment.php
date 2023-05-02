<?php

namespace MetaFox\HealthCheck\Checks;

use MetaFox\Platform\HealthCheck\Checker;
use MetaFox\Platform\HealthCheck\Result;
use MetaFox\Platform\MetaFox;

class CheckEnvironment extends Checker
{
    public function check(): Result
    {
        $result = $this->makeResult();

        if (config('app.debug', false)) {
            $result->error('The debug mode was expected to be false, Edit .env and set APP_DEBUG=false to improve performance.');
        }

        if ('production' !== config('app.env')) {
            $result->error(sprintf(
                'The app env was expected to be "production", but actually was "%s"',
                config('app.env')
            ));
        }

        if (!app()->configurationIsCached() ||
            !app()->routesAreCached() ||
            !app()->eventsAreCached()
        ) {
            $result->error(sprintf('Application is not running in optmization mode. Run `php artisan optimize` and try again'));
        }

        if (!config('app.mfox_license_id') || !config('app.mfox_license_key')) {
            $result->error('Missing metafox license [MFOX_LICENSE_ID, MFOX_LICENSE_KEY]');
        }

        if (file_exists(base_path('storage/framework/down'))) {
            $result->error(sprintf('%s is in maintenance mode, Run `php artisan up` and try again!',
                config('app.name')));
        }

        if (!file_exists(storage_path('oauth-private.key'))
            || !file_exists(storage_path('oauth-public.key'))
        ) {
            $result->error('Missing [./storage/oauth-private.key, ./storage/oauth-public.key]. run `php artisan passport:keys` and try again.');
        }

        if (ini_get('opcache.enabled') && config('app.mfox_preload_enabled')) {
            $expectedReload = base_path('preload.php');
            if ($expectedReload != ini_get('opcache.preload')) {
                $result->error(sprintf(
                    'missing php configuration opache.preload, but actually as "%s"',
                    $expectedReload
                ));
            }
        }

        $result->debug(sprintf('Operating System: %s', php_uname()));
        $result->debug(sprintf('Zend engine version: %s', zend_version()));
        $result->debug(sprintf('Platform Version: %s', MetaFox::getVersion()));
        $result->debug(sprintf('PHP Version: %s', phpversion()));
        $result->debug(sprintf('Max Execution Time: %s seconds', ini_get('max_execution_time')));
        $result->debug(sprintf('Memory Limit: %s', ini_get('memory_limit')));


        return $result;
    }

    public function getName()
    {
        return 'Environment Variables';
    }
}

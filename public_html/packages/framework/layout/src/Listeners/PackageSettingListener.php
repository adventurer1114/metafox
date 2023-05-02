<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Layout\Listeners;

use Illuminate\Console\Scheduling\Schedule;
use MetaFox\Layout\Jobs\CheckBuild;
use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * --------------------------------------------------------------------------
 * Code Generator
 * --------------------------------------------------------------------------
 * stub: src/Listeners/PackageSettingListener.stub.
 */

/**
 * Class PackageSettingListener.
 * @SuppressWarnings(PHPMD)
 * @ignore
 * @codeCoverageIgnore
 */
class PackageSettingListener extends BasePackageSettingListener
{
    public function registerApplicationSchedule(Schedule $schedule): void
    {
        $schedule->job(CheckBuild::class)
            ->everyMinute()
            ->withoutOverlapping()
            ->onOneServer();
    }
}

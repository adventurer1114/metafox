<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Backup\Listeners;

use Illuminate\Console\Scheduling\Schedule;
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
    /**
     * @param  Schedule $schedule
     * @return void
     * @link https://spatie.be/docs/laravel-backup/v8/installation-and-setup#content-scheduling
     */
    public function registerApplicationSchedule(Schedule $schedule)
    {
        $schedule->command('backup:clean')
            ->withoutOverlapping()
            ->daily()
            ->onOneServer()
            ->onFailure(function () {
            })
            ->onSuccess(function () {
            })
            ->at('01:00');

        $schedule->command('backup:run')
            ->withoutOverlapping()
            ->daily()
            ->onOneServer()
            ->onFailure(function () {
            })
            ->onSuccess(function () {
            })
            ->at('01:30');
    }

    public function getSiteSettings(): array
    {
        return [
            'keep_all_backups_for_days' => [
                'is_public'   => 0,
                'type'        => 'integer',
                'value'       => 7,
                'config_name' => 'backup.cleanup.default_strategy.keep_all_backups_for_days',
            ],
            'keep_daily_backups_for_days' => [
                'is_public'   => 0,
                'type'        => 'integer',
                'value'       => 16,
                'config_name' => 'backup.cleanup.default_strategy.keep_daily_backups_for_days',
            ],
            'keep_weekly_backups_for_weeks' => [
                'is_public'   => 0,
                'type'        => 'integer',
                'value'       => 8,
                'config_name' => 'backup.cleanup.default_strategy.keep_weekly_backups_for_weeks',
            ],
            'keep_monthly_backups_for_months' => [
                'is_public'   => 0,
                'type'        => 'integer',
                'value'       => 4,
                'config_name' => 'backup.cleanup.default_strategy.keep_monthly_backups_for_months',
            ],
            'keep_yearly_backups_for_years' => [
                'is_public'   => 0,
                'type'        => 'integer',
                'value'       => 2,
                'config_name' => 'backup.cleanup.default_strategy.keep_yearly_backups_for_years',
            ],
            'delete_oldest_backups_when_using_more_megabytes_than' => [
                'is_public'   => 0,
                'type'        => 'integer',
                'value'       => 5000,
                'config_name' => 'backup.cleanup.default_strategy.delete_oldest_backups_when_using_more_megabytes_than',
            ],
        ];
    }
}

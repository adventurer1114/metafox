<?php
/**
 * @author  developer@phpfox.com
 * @license phpfox.com
 */

namespace MetaFox\Core\Listeners;

use Illuminate\Support\Facades\Log;
use MetaFox\Core\Repositories\DriverRepositoryInterface;
use MetaFox\Platform\Facades\Settings;
use MetaFox\Platform\ModuleManager;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Support\BasePackageSettingListener;

/**
 * Handle module installed.
 *
 * Class PackageInstalledListener
 */
class PackageInstalledListener
{
    public function handle(string $package): void
    {
        $this->publishSiteSettings($package);
        $this->publishDrivers($package);
        $this->publishSiteStats($package);
    }

    /**
     * publish site settings into the database.
     *
     * @param string $package
     */
    private function publishSiteSettings(string $package): void
    {
        Log::channel('installation')->debug('publishSiteSettings', [$package]);

        /** @var null|BasePackageSettingListener $listener */
        $listener = PackageManager::getListener($package);

        if (!$listener) {
            return;
        }

        $moduleId = PackageManager::getAlias($package);

        $settings = $listener->getSiteSettings();

//        Log::channel('installation')->debug('setupPackageSettings', $settings);

        Settings::setupPackageSettings($moduleId, $settings);
    }

    /**
     * Import drivers from "resources/drivers.php".
     *
     * @param string $package
     */
    private function publishDrivers(string $package): void
    {
        Log::channel('installation')->debug('publishDrivers', [$package]);

        $drivers = PackageManager::readFile($package, 'resources/drivers.php');

        if ($drivers) {
            resolve(DriverRepositoryInterface::class)->setupDrivers($package, $drivers);
        }
    }

    private function publishSiteStats(string $package): void
    {
        if ($package !== 'metafox/core') {
            return;
        }

        $data   = ModuleManager::instance()->discoverSettings('getSiteStatContent');
        $icons  = [];
        foreach ($data as $config) {
            if (!is_array($config)) {
                continue;
            }

            $icons = array_merge($icons, $config);
        }

        Settings::save(['core.general.site_stat_icons' => $icons]);
    }
}

<?php

namespace MetaFox\SEO\Listeners;

use MetaFox\Platform\PackageManager;
use MetaFox\SEO\Repositories\MetaRepositoryInterface;

class PackageInstalledListener
{
    public function handle(string $package): void
    {
        $this->publishPages($package);
    }

    public function publishPages(string $package): void
    {
        $pages = PackageManager::readFile($package, 'resources/pages.php');

        if (empty($pages)) {
            return;
        }

        resolve(MetaRepositoryInterface::class)->setupSEOMetas($package, $pages);
    }
}

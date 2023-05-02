<?php

namespace MetaFox\Storage\Repositories\Eloquent;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use MetaFox\Platform\PackageManager;
use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\Storage\Models\Asset;
use MetaFox\Storage\Repositories\AssetRepositoryInterface;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class AssetRepository.
 */
class AssetRepository extends AbstractRepository implements AssetRepositoryInterface
{
    public function model()
    {
        return Asset::class;
    }

    /**
     * Get an array of files in the asset paths.
     *
     * @param string $path
     *
     * @return array<string>
     */
    private function listFilesRecursive(string $path): array
    {
        $files = [];

        foreach (File::files($path) as $file) {
            $files[] = $file->getPathname();
        }

        foreach (File::directories($path) as $directory) {
            foreach ($this->listFilesRecursive($directory) as $file) {
                $files[] = $file;
            }
        }

        return $files;
    }

    public function publishAssets(string $package): void
    {
        $assetPath = PackageManager::getAssetPath($package);
        $localRoot = base_path($assetPath);
        $moduleId  = PackageManager::getAlias($package);

        // check local file system
        if (!File::isDirectory($localRoot)) {
            Log::channel('installation')->debug(sprintf('%s has no assets', $package));

            return;
        }

        $files = $this->listFilesRecursive($localRoot);

        if (empty($files)) {
            return;
        }

        $copyToDir = config(sprintf('metafox.packages.%s.asset', $package));

        // use "alias" as assets directory
        if (!$copyToDir) {
            $copyToDir = config(sprintf('metafox.packages.%s.alias', $package));
        }

        $config = PackageManager::getConfig($package);

        $publishAssets = $config['shareAssets'] ?? [];

        foreach ($files as $file) {
            $localPath   = substr($file, strlen($localRoot) + 1);
            $name        = $publishAssets[$localPath] ?? null;

            $exists = $this->getModel()->newQuery()->where([
                'module_id'  => $moduleId,
                'package_id' => $package,
                'name'       => $name,
            ])->first();

            if ($exists) {
                continue;
            }

            $storageFile = app('storage')
                ->putFileAs('asset', 'assets/' . $copyToDir, $file, $localPath, [
                    'item_type' => Asset::ENTITY_TYPE,
                ]);

            $storageFile->refresh();

            if ($name) {
                $this->getModel()->newQuery()->create([
                    'module_id'  => $moduleId,
                    'package_id' => $package,
                    'local_path' => $localPath,
                    'name'       => $name,
                    'file_id'    => $storageFile->id,
                ]);
            }
        }
    }

    public function loadAssetSettings(): array
    {
        /** @var Collection<Asset> $rows */
        $rows = $this->getModel()->newQuery()
            ->whereIn('module_id', resolve('core.packages')->getActivePackageAliases())
            ->whereNotNull('name')
            ->cursor();

        $results = [];

        foreach ($rows as $row) {
            Arr::set($results, sprintf('%s.%s', $row->module_id, $row->name), $row->url);
        }

        return $results;
    }

    public function findByName(string $name): ?Asset
    {
        return $this->getModel()->newQuery()
            ->whereIn('module_id', resolve('core.packages')->getActivePackageAliases())
            ->where('name', $name)
            ->first();
    }
}

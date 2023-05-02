<?php

namespace MetaFox\Storage\Repositories;

use MetaFox\Storage\Models\Asset;
use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface Asset.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface AssetRepositoryInterface
{
    /**
     * publishAssets.
     *
     * @param  string $package
     * @return void
     */
    public function publishAssets(string $package): void;

    /**
     * loadAssetSettings.
     *
     * @return array<string>
     */
    public function loadAssetSettings(): array;

    /**
     * findAssetByName.
     *
     * @param  string $name
     * @return ?Asset
     */
    public function findByName(string $name): ?Asset;
}

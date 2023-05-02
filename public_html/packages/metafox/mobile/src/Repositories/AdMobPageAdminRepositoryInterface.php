<?php

namespace MetaFox\Mobile\Repositories;

use Illuminate\Support\Collection;
use Prettus\Repository\Eloquent\BaseRepository;
use MetaFox\Mobile\Models\AdMobPage as Model;
use MetaFox\Platform\Contracts\User;

/**
 * Interface AdMobConfig.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface AdMobPageAdminRepositoryInterface
{
    /**
     * @param  User              $user
     * @return Collection<Model>
     */
    public function getConfigForSettings(User $user): Collection;

    /**
     * @return array<int, mixed>
     */
    public function getPageOptions(): array;

    /**
     * @param int    $pageid
     * @param string $configType
     */
    public function canAddConfigToPage(int $pageId, string $configType): bool;
}

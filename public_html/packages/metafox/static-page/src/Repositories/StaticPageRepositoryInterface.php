<?php

namespace MetaFox\StaticPage\Repositories;

use Prettus\Repository\Eloquent\BaseRepository;

/**
 * Interface StaticPage.
 *
 * @mixin BaseRepository
 * stub: /packages/repositories/interface.stub
 */
interface StaticPageRepositoryInterface
{
    /**
     * @param  int  $id
     * @return bool
     */
    public function deleteStaticPage(int $id): bool;
}

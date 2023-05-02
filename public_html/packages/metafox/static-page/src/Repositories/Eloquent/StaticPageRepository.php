<?php

namespace MetaFox\StaticPage\Repositories\Eloquent;

use MetaFox\Platform\Repositories\AbstractRepository;
use MetaFox\StaticPage\Models\StaticPage;
use MetaFox\StaticPage\Repositories\StaticPageRepositoryInterface;

/**
 * stub: /packages/repositories/eloquent_repository.stub.
 */

/**
 * Class StaticPageRepository.
 */
class StaticPageRepository extends AbstractRepository implements StaticPageRepositoryInterface
{
    public function model()
    {
        return StaticPage::class;
    }

    public function deleteStaticPage(int $id): bool
    {
        return $this->delete($id);
    }
}

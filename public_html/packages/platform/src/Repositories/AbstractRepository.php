<?php

namespace MetaFox\Platform\Repositories;

use Illuminate\Support\Arr;
use MetaFox\Platform\Contracts\InputCleaner;
use MetaFox\Platform\Repositories\Contracts\AbstractRepositoryInterface;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Traits\Helpers\InputCleanerTrait;
use Prettus\Repository\Eloquent\BaseRepository;
use Prettus\Repository\Exceptions\RepositoryException;

/**
 * Class AbstractRepository.
 */
abstract class AbstractRepository extends BaseRepository implements AbstractRepositoryInterface, InputCleaner
{
    use InputCleanerTrait;

    protected $skipPresenter = true;

    /**
     * @param array $params
     *
     * @return $this
     * @throws RepositoryException
     */
    public function where(array $params): self
    {
        $this->applyConditions($params);

        return $this;
    }

    public function createMany(array $items): bool
    {
        foreach ($items as $item) {
            $this->create($item);
        }

        return true;
    }

    public function hasSponsorView(array $attributes): bool
    {
        $view        = Arr::get($attributes, 'view');
        $currentPage = Arr::get($attributes, 'current_page');
        $category    = Arr::get($attributes, 'category_id');

        if ($this->isNoSponsorView($view)) {
            return false;
        }

        if (1 < $currentPage) {
            return false;
        }

        if ($category) {
            return false;
        }

        return true;
    }

    public function isNoSponsorView(?string $view): bool
    {
        return in_array($view, [
            Browse::VIEW_MY,
            Browse::VIEW_SIMILAR,
        ]);
    }

    /**
     * return the case-insensitive LIKE operator bases on the current database driver.
     *
     * @return string
     */
    public function likeOperator(): string
    {
        return database_driver() == 'pgsql' ? 'ilike' : 'like';
    }
}

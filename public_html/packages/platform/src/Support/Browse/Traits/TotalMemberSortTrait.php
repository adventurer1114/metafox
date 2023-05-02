<?php

namespace MetaFox\Platform\Support\Browse\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Platform\Support\Browse\Contracts\HasTotalMemberSort;
use MetaFox\Platform\Support\Browse\Scopes\SortScope;

/**
 * Trait TotalMemberSortTrait
 * @package MetaFox\Platform\Support\Browse\Traits
 * @mixin HasTotalMemberSort
 * @mixin SortScope
 */
trait TotalMemberSortTrait
{
    public function applyTotalMemberSort(Builder $builder, Model $model): void
    {
        $table = $model->getTable();
        $sortType = $this->getSortType();
        $sortColumn = $this->getTotalMemberSortColumn();

        $builder->orderBy($this->alias($table, $sortColumn), $sortType);
        $builder->orderBy($this->alias($table, 'id'), $sortType);
    }

    public function getTotalMemberSort(): string
    {
        return self::SORT_MOST_MEMBER;
    }
}

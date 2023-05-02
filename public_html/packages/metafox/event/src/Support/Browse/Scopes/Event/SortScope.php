<?php

namespace MetaFox\Event\Support\Browse\Scopes\Event;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use MetaFox\Event\Support\Browse\Contracts\HasTotalInterestedSort;
use MetaFox\Event\Support\Browse\Traits\TotalInterestedSortTrait;
use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Contracts\HasTotalMemberSort;
use MetaFox\Platform\Support\Browse\Scopes\SortScope as PlatformSortScope;
use MetaFox\Platform\Support\Browse\Traits\TotalMemberSortTrait;

/**
 * Class SortScope.
 */
class SortScope extends PlatformSortScope implements HasTotalMemberSort, HasTotalInterestedSort
{
    use TotalMemberSortTrait;
    use TotalInterestedSortTrait;

    public const SORT_TOTAL_MEMBER_COLUMN = 'total_member';
    public const SORT_TOTAL_INTERESTED_COLUMN = 'total_interested';
    public const SORT_END_TIME = 'end_time';
    public const SORT_RANDOM = 'random';

    /**
     * @return array<int, string>
     */
    public static function getAllowSort(): array
    {
        return [
            Browse::SORT_MOST_VIEWED,
            Browse::SORT_LATEST,
            Browse::SORT_RECENT,
            Browse::SORT_MOST_LIKED,
            Browse::SORT_MOST_DISCUSSED,
            self::SORT_MOST_INTERESTED,
            self::SORT_MOST_MEMBER,
            self::SORT_END_TIME,
            self::SORT_RANDOM,
        ];
    }

    public function getTotalMemberSortColumn(): string
    {
        return self::SORT_TOTAL_MEMBER_COLUMN;
    }

    public function getTotalInterestedSortColumn(): string
    {
        return self::SORT_TOTAL_INTERESTED_COLUMN;
    }

    /**
     * @param  Builder  $builder
     * @param  Model    $model
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function apply(Builder $builder, Model $model)
    {
        $table = $model->getTable();

        $sort = $this->getSort();
        $sortType = $this->getSortType();
        switch ($sort) {
            case self::SORT_MOST_INTERESTED:
                $builder->orderBy($this->alias($table, 'total_interested'), $sortType);
                $builder->orderBy($this->alias($table, 'id'), $sortType);
                break;
            case self::SORT_END_TIME:
                $builder->orderBy($this->alias($table, 'end_time'), $sortType);
                $builder->orderBy($this->alias($table, 'id'), $sortType);
                break;
            case self::SORT_RANDOM:
                $builder->inRandomOrder();
                break;
            case Browse::SORT_MOST_DISCUSSED:
                $builder->orderBy($this->alias($table, 'total_feed'), $sortType);
                $builder->orderBy($this->alias($table, 'id'), $sortType);
                break;
            default:
                parent::apply($builder, $model);
        }
    }
}

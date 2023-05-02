<?php

namespace MetaFox\Video\Support\Browse\Scopes\Video;

use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Contracts\HasAlphabetSort;
use MetaFox\Platform\Support\Browse\Scopes\SortScope as BaseScope;
use MetaFox\Platform\Support\Browse\Traits\AlphabetSortTrait;

/**
 * Class SortScope.
 */
class SortScope extends BaseScope implements HasAlphabetSort
{
    use AlphabetSortTrait;

    public const SORT_ALPHABETICAL_COLUMN = 'title';

    /**
     * @return array<int, string>
     */
    public static function getAllowSort(): array
    {
        return [
            Browse::SORT_RECENT,
            Browse::SORT_LATEST,
            Browse::SORT_MOST_LIKED,
            Browse::SORT_MOST_VIEWED,
            Browse::SORT_MOST_DISCUSSED,
            Browse::SORT_A_TO_Z,
            Browse::SORT_Z_TO_A,
        ];
    }

    public function getAlphabetSortColumn(): string
    {
        return self::SORT_ALPHABETICAL_COLUMN;
    }
}

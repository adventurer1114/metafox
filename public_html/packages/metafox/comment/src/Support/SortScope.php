<?php

namespace MetaFox\Comment\Support;

use MetaFox\Platform\Support\Browse\Browse;
use MetaFox\Platform\Support\Browse\Contracts\HasFeatureSort;
use MetaFox\Platform\Support\Browse\Contracts\HasTotalMemberSort;
use MetaFox\Platform\Support\Browse\Scopes\SortScope as PlatformSortScope;
use MetaFox\Platform\Support\Browse\Traits\FeatureSortTrait;
use MetaFox\Platform\Support\Browse\Traits\TotalMemberSortTrait;

class SortScope
{
    use TotalMemberSortTrait;
    use FeatureSortTrait;

    public const SORT_TOTAL_MEMBER_COLUMN = 'total_member';
    public const SORT_FEATURE_COLUMN      = 'is_feature';

    /**
     * @return array<int, string>
     */
    public static function getAllowSort(): array
    {
        return [
            Browse::SORT_LATEST,
            Browse::SORT_RECENT,
        ];
    }

    /**
     * @return array<int, string>
     */
    public static function getAllowSortType(): array
    {
        return [
            Browse::SORT_TYPE_DESC,
            Browse::SORT_TYPE_ASC,
        ];
    }
}

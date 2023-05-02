<?php

namespace MetaFox\Sticker\Support\Browse\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use MetaFox\Platform\Support\Browse\Scopes\BaseScope;

/**
 * Class RecentScope.
 * @ignore
 * @codeCoverageIgnore
 */
class RecentScope extends BaseScope
{
    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function apply(Builder $builder, Model $model)
    {
        $builder->join('sticker_recent as sr', function (JoinClause $join) {
            $join->on('sr.sticker_id', '=', 'stickers.id');
        })->orderBy('sr.id', 'desc');
    }
}

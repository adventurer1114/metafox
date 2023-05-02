<?php

namespace MetaFox\Photo\Support\Browse\Scopes\Album;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\JoinClause;
use MetaFox\Platform\Support\Browse\Scopes\PrivacyScope as Main;

class PrivacyScope extends Main
{
    protected function addPrivacyScope(Builder $builder, Model $model): void
    {
        $builder->leftJoin('core_privacy_streams as stream', function (JoinClause $leftJoin) {
            $leftJoin->on('stream.item_id', '=', 'photo_album_item.item_id');
            $leftJoin->on('stream.item_type', '=', 'photo_album_item.item_type');
        });
    }

    protected function addBlockedScope(Builder $builder, Model $model): void
    {
    }
}

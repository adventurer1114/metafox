<?php

namespace MetaFox\Core\Http\Resources\v1\Statistic;

use Illuminate\Http\Resources\Json\ResourceCollection;

class StatisticItemCollection extends ResourceCollection
{
    public $collects = StatisticItem::class;
}

<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItem;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * @ignore
 * @codeCoverageIgnore
 */
class ReportItemItemCollection extends ResourceCollection
{
    public $collects = ReportItemItem::class;
}

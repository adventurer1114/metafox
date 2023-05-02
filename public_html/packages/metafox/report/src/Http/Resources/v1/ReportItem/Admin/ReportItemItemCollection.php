<?php

namespace MetaFox\Report\Http\Resources\v1\ReportItem\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportItemItemCollection extends ResourceCollection
{
    public $collects = ReportItemItem::class;
}

<?php

namespace MetaFox\Report\Http\Resources\v1\ReportOwner;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportOwnerItemCollection extends ResourceCollection
{
    public $collects = ReportOwnerItem::class;
}

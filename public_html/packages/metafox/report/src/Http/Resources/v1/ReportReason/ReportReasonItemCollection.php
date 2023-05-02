<?php

namespace MetaFox\Report\Http\Resources\v1\ReportReason;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReportReasonItemCollection extends ResourceCollection
{
    public $collects = ReportReasonItem::class;
}

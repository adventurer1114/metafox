<?php

namespace MetaFox\Quiz\Http\Resources\v1\ResultDetail;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ResultDetailItemCollection extends ResourceCollection
{
    public $collects = ResultDetailItem::class;
}

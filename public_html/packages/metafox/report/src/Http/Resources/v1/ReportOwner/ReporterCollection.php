<?php

namespace MetaFox\Report\Http\Resources\v1\ReportOwner;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ReporterCollection extends ResourceCollection
{
    /**
     * @var string
     */
    public $collects = Reporter::class;
}

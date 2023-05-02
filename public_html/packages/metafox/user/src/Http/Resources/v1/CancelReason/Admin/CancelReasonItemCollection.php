<?php

namespace MetaFox\User\Http\Resources\v1\CancelReason\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CancelReasonItemCollection extends ResourceCollection
{
    public $collects = CancelReasonItem::class;
}

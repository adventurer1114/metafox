<?php

namespace MetaFox\Payment\Http\Resources\v1\Gateway;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GatewayItemCollection extends ResourceCollection
{
    public $collects = GatewayItem::class;
}

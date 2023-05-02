<?php

namespace MetaFox\Payment\Http\Resources\v1\Gateway;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GatewayConfigurationItemCollection extends ResourceCollection
{
    public $collects = GatewayConfigurationItem::class;
}

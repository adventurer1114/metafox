<?php

namespace MetaFox\Localize\Http\Resources\v1\Timezone;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class TimezoneItemCollection.
 */
class TimezoneItemCollection extends ResourceCollection
{
    public $collects = TimezoneItem::class;
}

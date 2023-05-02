<?php

namespace MetaFox\Group\Http\Resources\v1\Request;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class RequestItemCollection.
 */
class RequestItemCollection extends ResourceCollection
{
    public $collects = RequestItem::class;
}

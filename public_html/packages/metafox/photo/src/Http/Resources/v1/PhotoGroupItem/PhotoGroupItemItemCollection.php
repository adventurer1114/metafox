<?php

namespace MetaFox\Photo\Http\Resources\v1\PhotoGroupItem;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhotoGroupItemItemCollection extends ResourceCollection
{
    /**
     * @var string
     */
    protected $collect = PhotoGroupItemItem::class;
}

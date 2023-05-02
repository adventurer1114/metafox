<?php

namespace MetaFox\Photo\Http\Resources\v1\Photo;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhotoItemCollection extends ResourceCollection
{
    public $collects = PhotoItem::class;
}

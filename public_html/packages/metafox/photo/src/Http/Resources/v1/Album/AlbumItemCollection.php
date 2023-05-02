<?php

namespace MetaFox\Photo\Http\Resources\v1\Album;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AlbumItemCollection extends ResourceCollection
{
    public $collects = AlbumItem::class;
}

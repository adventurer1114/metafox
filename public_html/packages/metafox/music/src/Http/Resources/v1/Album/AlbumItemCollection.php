<?php

namespace MetaFox\Music\Http\Resources\v1\Album;

use Illuminate\Http\Resources\Json\ResourceCollection;
use MetaFox\Music\Http\Resources\v1\Album\AlbumItem;

class AlbumItemCollection extends ResourceCollection
{
    protected string $collect = AlbumItem::class;
}

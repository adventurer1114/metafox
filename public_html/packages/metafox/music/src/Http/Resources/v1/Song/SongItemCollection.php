<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SongItemCollection extends ResourceCollection
{
    protected string $collect = SongItem::class;
}

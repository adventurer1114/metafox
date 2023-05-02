<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SongPlayCollection extends ResourceCollection
{
    public $collects = SongPlayItem::class;
}

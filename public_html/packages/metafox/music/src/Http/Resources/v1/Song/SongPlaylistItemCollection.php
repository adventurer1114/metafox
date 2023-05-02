<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SongPlaylistItemCollection extends ResourceCollection
{
    public $collects = SongPlaylistItem::class;
}

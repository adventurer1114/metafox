<?php

namespace MetaFox\Music\Http\Resources\v1\Playlist;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaylistItemCollection extends ResourceCollection
{
    protected string $collect = PlaylistItem::class;
}

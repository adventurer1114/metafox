<?php

namespace MetaFox\Music\Http\Resources\v1\Playlist;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PlaylistEmbedCollection extends ResourceCollection
{
    /** @var string */
    protected $collect = PlaylistEmbed::class;
}

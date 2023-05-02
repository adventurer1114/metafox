<?php

namespace MetaFox\Music\Http\Resources\v1\Album;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AlbumEmbedCollection extends ResourceCollection
{
    /** @var string */
    protected $collect = AlbumEmbed::class;
}

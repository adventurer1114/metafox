<?php

namespace MetaFox\Music\Http\Resources\v1\Song;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SongEmbedCollection extends ResourceCollection
{
    /** @var string */
    protected $collect = SongEmbed::class;
}

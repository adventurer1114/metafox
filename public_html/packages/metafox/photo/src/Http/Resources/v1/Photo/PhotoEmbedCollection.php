<?php

namespace MetaFox\Photo\Http\Resources\v1\Photo;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhotoEmbedCollection extends ResourceCollection
{
    public $collects = PhotoEmbed::class;
}

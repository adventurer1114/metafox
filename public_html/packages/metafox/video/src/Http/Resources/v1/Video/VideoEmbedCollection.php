<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VideoEmbedCollection extends ResourceCollection
{
    public $collects = VideoEmbed::class;
}

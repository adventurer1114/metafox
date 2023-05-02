<?php

namespace MetaFox\Video\Http\Resources\v1\Video;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VideoItemCollection extends ResourceCollection
{
    protected string $collect = VideoItem::class;
}

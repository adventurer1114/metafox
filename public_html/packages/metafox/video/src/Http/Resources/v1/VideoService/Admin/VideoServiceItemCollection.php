<?php

namespace MetaFox\Video\Http\Resources\v1\VideoService\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VideoServiceItemCollection extends ResourceCollection
{
    public $collects = VideoServiceItem::class;
}

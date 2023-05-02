<?php

namespace MetaFox\Music\Http\Resources\v1\Genre\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class GenreItemCollection extends ResourceCollection
{
    public $collects = GenreItem::class;
}

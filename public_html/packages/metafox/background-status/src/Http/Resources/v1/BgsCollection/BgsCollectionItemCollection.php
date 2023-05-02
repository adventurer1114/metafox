<?php

namespace MetaFox\BackgroundStatus\Http\Resources\v1\BgsCollection;

use Illuminate\Http\Resources\Json\ResourceCollection;

class BgsCollectionItemCollection extends ResourceCollection
{
    public bool $preserveKeys = true;
    public $collects          = BgsCollectionItem::class;
}

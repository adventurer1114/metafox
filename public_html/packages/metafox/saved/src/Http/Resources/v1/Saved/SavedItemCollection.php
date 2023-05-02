<?php

namespace MetaFox\Saved\Http\Resources\v1\Saved;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SavedItemCollection extends ResourceCollection
{
    public $collects = SavedItem::class;
}

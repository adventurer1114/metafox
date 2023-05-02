<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedList;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SavedListItemCollection extends ResourceCollection
{
    public $collects = SavedListItem::class;
}

<?php

namespace MetaFox\Saved\Http\Resources\v1\SavedList;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SavedListDataItemCollection extends ResourceCollection
{
    public $collects = SavedListDataItem::class;
}

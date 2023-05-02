<?php

namespace MetaFox\Storage\Http\Resources\v1\StorageFile;

use Illuminate\Http\Resources\Json\ResourceCollection;
use MetaFox\Storage\Models\StorageFile;

/**
 * @property StorageFile $resource
 */
class StorageFileCollection extends ResourceCollection
{
    public $collects = StorageFileItem::class;
}

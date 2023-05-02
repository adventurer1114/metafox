<?php

namespace MetaFox\Group\Http\Resources\v1\Announcement;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class AnnouncementItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class AnnouncementItemCollection extends ResourceCollection
{
    public $collects = AnnouncementItem::class;
}

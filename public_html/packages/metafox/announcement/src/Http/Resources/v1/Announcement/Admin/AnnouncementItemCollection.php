<?php

namespace MetaFox\Announcement\Http\Resources\v1\Announcement\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

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

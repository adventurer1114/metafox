<?php

namespace MetaFox\Page\Http\Resources\v1\PageClaim\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Request;

/**
|--------------------------------------------------------------------------
| Resource Pattern
|--------------------------------------------------------------------------
| stub: /packages/resources/item_collection.stub
 */

/**
 * Class PageClaimItemCollection.
 * @ignore
 * @codeCoverageIgnore
 */
class PageClaimItemCollection extends ResourceCollection
{
    public $collects = PageClaimItem::class;
}

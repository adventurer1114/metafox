<?php

namespace MetaFox\Core\Http\Resources\v1\SiteSetting\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class SiteSettingItemCollection extends ResourceCollection
{
    public $collects = SiteSettingItem::class;
}

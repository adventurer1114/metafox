<?php

namespace MetaFox\Menu\Http\Resources\v1\MenuItem\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuItemItemCollection extends ResourceCollection
{
    public $collects = MenuItemItem::class;
}

<?php

namespace MetaFox\Menu\Http\Resources\v1\Menu\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MenuItemCollection extends ResourceCollection
{
    public $collects = MenuItem::class;
}

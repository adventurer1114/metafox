<?php

namespace MetaFox\Localize\Http\Resources\v1\Country\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryItemCollection extends ResourceCollection
{
    public $collects = CountryItem::class;
}

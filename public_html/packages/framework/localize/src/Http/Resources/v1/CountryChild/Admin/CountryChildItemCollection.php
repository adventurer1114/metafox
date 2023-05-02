<?php

namespace MetaFox\Localize\Http\Resources\v1\CountryChild\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CountryChildItemCollection extends ResourceCollection
{
    public $collects = CountryChildItem::class;
}

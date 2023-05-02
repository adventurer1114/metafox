<?php

namespace MetaFox\Localize\Http\Resources\v1\Currency\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CurrencyItemCollection extends ResourceCollection
{
    public $collects = CurrencyItem::class;
}

<?php

namespace MetaFox\Core\Http\Resources\v1\Privacy;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomPrivacyOptionCollection extends ResourceCollection
{
    public $collects = CustomPrivacyOptionItem::class;
}

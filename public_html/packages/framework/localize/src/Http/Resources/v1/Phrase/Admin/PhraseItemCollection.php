<?php

namespace MetaFox\Localize\Http\Resources\v1\Phrase\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhraseItemCollection extends ResourceCollection
{
    public $collects = PhraseItem::class;
}

<?php

namespace MetaFox\Group\Http\Resources\v1\Rule;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class RuleItemCollection.
 */
class RuleItemCollection extends ResourceCollection
{
    public $collects = RuleItem::class;
}

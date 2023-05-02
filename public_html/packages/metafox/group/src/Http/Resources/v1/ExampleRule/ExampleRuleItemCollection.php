<?php

namespace MetaFox\Group\Http\Resources\v1\ExampleRule;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class ExampleRuleItemCollection.
 */
class ExampleRuleItemCollection extends ResourceCollection
{
    public $collects = ExampleRuleItem::class;
}

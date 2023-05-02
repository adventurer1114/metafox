<?php

namespace MetaFox\Group\Http\Resources\v1\Answers;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class AnswersItemCollection.
 */
class AnswersItemCollection extends ResourceCollection
{
    public $collects = AnswersItem::class;
}

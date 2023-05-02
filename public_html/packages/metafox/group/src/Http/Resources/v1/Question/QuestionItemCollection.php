<?php

namespace MetaFox\Group\Http\Resources\v1\Question;

use Illuminate\Http\Resources\Json\ResourceCollection;

/**
 * Class QuestionItemCollection.
 */
class QuestionItemCollection extends ResourceCollection
{
    public bool $preserveKeys = true;

    public $collects = QuestionItem::class;
}

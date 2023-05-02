<?php

namespace MetaFox\Quiz\Http\Resources\v1\Question;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionItemCollection extends ResourceCollection
{
    public $collects = QuestionItem::class;
}

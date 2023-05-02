<?php

namespace MetaFox\Quiz\Http\Resources\v1\Answer;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AnswerItemCollection extends ResourceCollection
{
    public $collects = AnswerItem::class;
}

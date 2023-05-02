<?php

namespace MetaFox\Quiz\Http\Resources\v1\Quiz;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuizItemCollection extends ResourceCollection
{
    public $collects = QuizItem::class;
}

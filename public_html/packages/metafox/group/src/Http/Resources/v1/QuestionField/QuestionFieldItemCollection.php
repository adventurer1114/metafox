<?php

namespace MetaFox\Group\Http\Resources\v1\QuestionField;

use Illuminate\Http\Resources\Json\ResourceCollection;

class QuestionFieldItemCollection extends ResourceCollection
{
    public $collects = QuestionFieldItem::class;
}

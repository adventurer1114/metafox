<?php

namespace MetaFox\User\Http\Resources\v1\CancelFeedback\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CancelFeedbackItemCollection extends ResourceCollection
{
    public $collects = CancelFeedbackItem::class;
}

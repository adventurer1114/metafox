<?php

namespace MetaFox\Chat\Http\Resources\v1\Message;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageAttachmentCollection extends ResourceCollection
{
    public $collects = MessageAttachmentItem::class;
}

<?php

namespace MetaFox\Core\Http\Resources\v1\Attachment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AttachmentItemCollection extends ResourceCollection
{
    public $collects = AttachmentItem::class;
}

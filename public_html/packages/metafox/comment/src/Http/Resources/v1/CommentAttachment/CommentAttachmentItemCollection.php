<?php

namespace MetaFox\Comment\Http\Resources\v1\CommentAttachment;

use Illuminate\Http\Resources\Json\ResourceCollection;

class CommentAttachmentItemCollection extends ResourceCollection
{
    public $collects = CommentAttachmentItem::class;
}

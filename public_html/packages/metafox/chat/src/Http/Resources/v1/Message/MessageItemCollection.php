<?php
namespace MetaFox\Chat\Http\Resources\v1\Message;

use Illuminate\Http\Resources\Json\ResourceCollection;

class MessageItemCollection extends ResourceCollection
{
    protected $collect = MessageItem::class;
}

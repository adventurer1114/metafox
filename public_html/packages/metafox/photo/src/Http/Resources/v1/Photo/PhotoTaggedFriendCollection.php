<?php

namespace MetaFox\Photo\Http\Resources\v1\Photo;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PhotoTaggedFriendCollection extends ResourceCollection
{
    public $collects = PhotoTaggedFriend::class;
}

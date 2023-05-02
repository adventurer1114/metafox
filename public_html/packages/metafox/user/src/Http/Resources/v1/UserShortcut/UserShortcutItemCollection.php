<?php

namespace MetaFox\User\Http\Resources\v1\UserShortcut;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserShortcutItemCollection extends ResourceCollection
{
    public $collects = UserShortcutItem::class;
}

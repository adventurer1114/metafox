<?php

namespace MetaFox\User\Http\Resources\v1\UserPromotion\Admin;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserPromotionItemCollection extends ResourceCollection
{
    public $collects = UserPromotionItem::class;
}

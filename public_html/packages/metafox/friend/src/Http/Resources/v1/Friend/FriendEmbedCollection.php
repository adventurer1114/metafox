<?php

namespace MetaFox\Friend\Http\Resources\v1\Friend;

use Illuminate\Http\Resources\Json\ResourceCollection;

/*
|--------------------------------------------------------------------------
| Resource Collection
|--------------------------------------------------------------------------
|
| @link https://laravel.com/docs/8.x/eloquent-resources#concept-overview
| @link /app/Console/Commands/stubs/module/resources/detail.stub
|
*/

class FriendEmbedCollection extends ResourceCollection
{
    public $collects = FriendEmbed::class;
}

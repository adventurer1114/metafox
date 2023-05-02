<?php

namespace MetaFox\Group\Http\Resources\v1\Group;

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

class GroupEmbedCollection extends ResourceCollection
{
    public $collects = GroupEmbed::class;
}

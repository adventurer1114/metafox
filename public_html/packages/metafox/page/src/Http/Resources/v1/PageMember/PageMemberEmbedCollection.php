<?php

namespace MetaFox\Page\Http\Resources\v1\PageMember;

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

class PageMemberEmbedCollection extends ResourceCollection
{
    public $collects = PageMemberEmbed::class;
}

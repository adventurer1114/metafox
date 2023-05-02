<?php

namespace MetaFox\Page\Http\Resources\v1\Page;

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

class PageEmbedCollection extends ResourceCollection
{
    public $collects = PageEmbed::class;
}

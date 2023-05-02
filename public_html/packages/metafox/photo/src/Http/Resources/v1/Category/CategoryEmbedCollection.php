<?php

namespace MetaFox\Photo\Http\Resources\v1\Category;

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

class CategoryEmbedCollection extends ResourceCollection
{
    public $collects = CategoryEmbed::class;
}

<?php

namespace MetaFox\Blog\Http\Resources\v1\Blog;

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

class BlogEmbedCollection extends ResourceCollection
{
    /** @var string */
    protected $collect = BlogEmbed::class;
}
